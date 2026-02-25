package com.quizmaster.ui.screens

import androidx.compose.animation.*
import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.text.withStyle
import androidx.compose.ui.text.buildAnnotatedString
import androidx.compose.ui.text.SpanStyle
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Star
import com.quizmaster.data.model.Option
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.components.PremiumButton
import com.quizmaster.ui.components.PremiumLoadingScreen
import com.quizmaster.ui.components.SegmentedProgressBar
import com.quizmaster.ui.theme.*
import com.quizmaster.ui.viewmodel.QuizViewModel
import kotlinx.coroutines.delay

@Composable
fun QuizScreen(
    viewModel: QuizViewModel,
    onQuizComplete: () -> Unit
) {
    val questions = viewModel.questions.value
    val currentIndex = viewModel.currentQuestionIndex.value
    val configTimer = viewModel.quizTimer.value
    val isLoading = viewModel.isLoading.value
    val streak = viewModel.streak.value

    val haptic = androidx.compose.ui.platform.LocalHapticFeedback.current

    var timeLeft by remember(currentIndex) { mutableStateOf(configTimer) }
    var selectedAnswer by remember(currentIndex) { mutableStateOf<Int?>(null) }
    var isAnswerChecked by remember(currentIndex) { mutableStateOf(false) }
    var isCorrectSelection by remember(currentIndex) { mutableStateOf(false) }
    var isTimeout by remember(currentIndex) { mutableStateOf(false) }

    val currentQuestion = if (questions.isNotEmpty()) questions[currentIndex] else null
    val offsetX = remember(currentIndex) { androidx.compose.animation.core.Animatable(0f) }

    // Timer Logic
    LaunchedEffect(currentIndex, isAnswerChecked) {
        if (!isAnswerChecked) {
            timeLeft = configTimer
            while (timeLeft > 0) {
                kotlinx.coroutines.delay(1000L)
                timeLeft--
            }
            if (timeLeft == 0 && !isAnswerChecked) {
                // Auto-submit or handle timeout
                isTimeout = true
                isAnswerChecked = true
                isCorrectSelection = false // Timeout is wrong
                viewModel.saveAnswer(currentQuestion?.id ?: -1, null, 0)
            }
        }
    }


    Box(
        modifier = Modifier
            .fillMaxSize()
    ) {
        if (isLoading || currentQuestion == null) {
            PremiumLoadingScreen(message = "Fetching Question...")
        } else {
            key(currentIndex) {
                val scrollState = rememberScrollState()
                
                // Auto-scroll to explanation when it appears
                LaunchedEffect(isAnswerChecked) {
                    if (isAnswerChecked) {
                        scrollState.animateScrollTo(scrollState.maxValue)
                    }
                }

                // Shake Animation Logic
                LaunchedEffect(isAnswerChecked) {
                    if (isAnswerChecked && !isCorrectSelection && !isTimeout) {
                        try {
                            repeat(3) {
                                offsetX.animateTo(15f, tween(60, easing = LinearEasing))
                                offsetX.animateTo(-15f, tween(60, easing = LinearEasing))
                            }
                            offsetX.animateTo(0f, tween(60, easing = FastOutSlowInEasing))
                        } catch (e: Exception) {
                            offsetX.snapTo(0f)
                        }
                    }
                }

                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .offset(x = offsetX.value.dp)
                        .padding(horizontal = 20.dp, vertical = 12.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    // Main content area that scrolls
                    Column(
                        modifier = Modifier
                            .weight(1f)
                            .verticalScroll(scrollState),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        // Top Stats Row (Side by Side)
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(16.dp)
                        ) {
                            // Timer Card (Left)
                            GlassyCard(
                                modifier = Modifier.weight(1.1f),
                                shape = RoundedCornerShape(12.dp),
                                containerColor = Color(0xFF1E293B).copy(alpha = 0.4f),
                                contentPadding = 10.dp
                            ) {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Box(contentAlignment = Alignment.Center) {
                                        CircularProgressIndicator(
                                            progress = timeLeft.toFloat() / configTimer.coerceAtLeast(1),
                                            modifier = Modifier.size(36.dp),
                                            color = if (timeLeft <= 5) Color.Red else Color(0xFFFBBF24),
                                            strokeWidth = 3.dp,
                                            trackColor = Color.White.copy(alpha = 0.1f)
                                        )
                                        Text(
                                            text = "$timeLeft",
                                            color = Color.White,
                                            fontSize = 12.sp,
                                            fontWeight = FontWeight.Black
                                        )
                                    }
                                    Spacer(modifier = Modifier.width(10.dp))
                                    Column(verticalArrangement = Arrangement.Center) {
                                        Text(
                                            text = "SISA WAKTU",
                                            color = Color.White.copy(alpha = 0.7f),
                                            fontSize = 11.sp,
                                            fontWeight = FontWeight.ExtraBold,
                                            letterSpacing = 0.5.sp
                                        )
                                        Text(
                                            text = "detik",
                                            color = Color(0xFFFBBF24),
                                            fontSize = 10.sp,
                                            fontWeight = FontWeight.Bold,
                                            modifier = Modifier.offset(y = (-2).dp)
                                        )
                                    }
                                }
                            }

                            // Score Card (Right)
                            GlassyCard(
                                modifier = Modifier.weight(1.3f),
                                shape = RoundedCornerShape(12.dp),
                                containerColor = Color(0xFF1E293B).copy(alpha = 0.4f),
                                contentPadding = 10.dp
                            ) {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Icon(
                                        imageVector = androidx.compose.material.icons.Icons.Default.Star,
                                        contentDescription = null,
                                        tint = Color(0xFF3B82F6),
                                        modifier = Modifier.size(32.dp)
                                    )
                                    Spacer(modifier = Modifier.width(8.dp))
                                    Column(verticalArrangement = Arrangement.Center) {
                                        Text(
                                            text = "SKOR SEMENTARA",
                                            color = Color.White.copy(alpha = 0.7f),
                                            fontSize = 11.sp,
                                            fontWeight = FontWeight.ExtraBold,
                                            letterSpacing = 0.5.sp
                                        )
                                        Text(
                                            text = "${viewModel.currentScore.value}", 
                                            color = Color(0xFF3B82F6),
                                            fontSize = 20.sp,
                                            fontWeight = FontWeight.Black,
                                            modifier = Modifier.offset(y = (-2).dp)
                                        )
                                    }
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(8.dp))

                        GlassyCard(
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            containerColor = Color(0xFF1E293B).copy(alpha = 0.6f),
                            contentPadding = 8.dp
                        ) {
                            Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.Bottom
                                ) {
                                    Row(verticalAlignment = Alignment.Bottom) {
                                        Text(
                                            text = "Soal ${currentIndex + 1}",
                                            fontSize = 12.sp,
                                            fontWeight = FontWeight.Black,
                                            color = Color.White
                                        )
                                        Spacer(modifier = Modifier.width(4.dp))
                                        Text(
                                            text = "/ ${questions.size}",
                                            color = Color.White.copy(alpha = 0.4f),
                                            fontSize = 10.sp,
                                            fontWeight = FontWeight.Bold
                                        )
                                    }
                                    
                                    Text(
                                        text = "${((currentIndex + 1).toFloat() / questions.size * 100).toInt()}%",
                                        color = Color.White.copy(alpha = 0.4f),
                                        fontSize = 10.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                }
                                
                                SegmentedProgressBar(
                                    count = questions.size,
                                    currentIndex = currentIndex,
                                    getStatus = { i -> viewModel.getQuestionStatus(i) }
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(8.dp))

                        GlassyCard(
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(16.dp),
                            containerColor = Color(0xFF1E293B).copy(alpha = 0.4f)
                        ) {
                            Column(modifier = Modifier.padding(14.dp)) {
                                Text(
                                    text = currentQuestion.questionText,
                                    fontSize = 18.sp,
                                    fontWeight = FontWeight.ExtraBold,
                                    color = Color.White,
                                    textAlign = TextAlign.Start,
                                    lineHeight = 24.sp,
                                    modifier = Modifier.fillMaxWidth()
                                )
                                
                                Spacer(modifier = Modifier.height(12.dp))
                                
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Icon(
                                        imageVector = Icons.Default.Info,
                                        contentDescription = null,
                                        tint = Color.White.copy(alpha = 0.4f),
                                        modifier = Modifier.size(14.dp)
                                    )
                                    Spacer(modifier = Modifier.width(6.dp))
                                    Text(
                                        text = "Pilih satu jawaban yang benar.",
                                        color = Color.White.copy(alpha = 0.4f),
                                        fontSize = 12.sp,
                                        fontStyle = androidx.compose.ui.text.font.FontStyle.Italic
                                    )
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(12.dp))

                        Column(
                            modifier = Modifier.fillMaxWidth(),
                            verticalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            currentQuestion.options.forEachIndexed { index, option ->
                                PremiumOptionButton(
                                    option = option,
                                    index = index,
                                    isSelected = selectedAnswer == option.id,
                                    isAnswerChecked = isAnswerChecked,
                                    onClick = {
                                        if (!isAnswerChecked) {
                                            haptic.performHapticFeedback(androidx.compose.ui.hapticfeedback.HapticFeedbackType.TextHandleMove)
                                            selectedAnswer = option.id
                                        }
                                    }
                                )
                            }
                        }

                        if (isAnswerChecked) {
                            Spacer(modifier = Modifier.height(24.dp))
                            
                            GlassyCard(
                                modifier = Modifier.fillMaxWidth(),
                                containerColor = Color.White.copy(alpha = 0.05f),
                                borderColor = if (isTimeout) Color.Red.copy(alpha = 0.5f) else Color(0xFFFBBF24).copy(alpha = 0.5f)
                            ) {
                                Column {
                                    Row(verticalAlignment = Alignment.CenterVertically) {
                                        Icon(
                                            imageVector = Icons.Default.Info,
                                            contentDescription = null,
                                            tint = if (isTimeout) Color.Red else Color(0xFFFBBF24),
                                            modifier = Modifier.size(16.dp)
                                        )
                                        Spacer(modifier = Modifier.width(8.dp))
                                        Text(
                                            text = if (isTimeout) "Waktu Habis!" else "Pembahasan",
                                            color = if (isTimeout) Color.Red else Color(0xFFFBBF24),
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 16.sp
                                        )
                                    }
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text(
                                        text = currentQuestion.explanation ?: "Tidak ada pembahasan.",
                                        color = Color.White.copy(alpha = 0.8f),
                                        fontSize = 14.sp,
                                        lineHeight = 20.sp
                                    )
                                    if (!currentQuestion.reference.isNullOrBlank()) {
                                        Spacer(modifier = Modifier.height(8.dp))
                                        Text(
                                            text = "📚 Sumber: ${currentQuestion.reference}",
                                            color = Color.White.copy(alpha = 0.4f),
                                            fontSize = 12.sp,
                                            fontStyle = androidx.compose.ui.text.font.FontStyle.Italic
                                        )
                                    }
                                }
                            }
                            Spacer(modifier = Modifier.height(16.dp))
                        }
                    }

                    // Navigation buttons area fixed at bottom
                    Spacer(modifier = Modifier.height(8.dp))

                    if (!isAnswerChecked) {
                        PremiumButton(
                            text = "Check Answer ✓",
                            onClick = {
                                if (selectedAnswer != null) {
                                    isCorrectSelection = viewModel.saveAnswer(currentQuestion.id, selectedAnswer, timeLeft)
                                    isAnswerChecked = true
                                    
                                    // Laravel Parity Haptics
                                    if (isCorrectSelection) {
                                        haptic.performHapticFeedback(androidx.compose.ui.hapticfeedback.HapticFeedbackType.TextHandleMove)
                                    } else {
                                        haptic.performHapticFeedback(androidx.compose.ui.hapticfeedback.HapticFeedbackType.LongPress)
                                    }
                                }
                            },
                            enabled = selectedAnswer != null,
                            modifier = Modifier.fillMaxWidth()
                        )
                    } else {
                        PremiumButton(
                            text = if (currentIndex < questions.size - 1) "Next Question →" else "Finish Quiz ✓",
                            onClick = {
                                if (currentIndex < questions.size - 1) {
                                    viewModel.nextQuestion()
                                } else {
                                    viewModel.submitQuiz()
                                    onQuizComplete()
                                }
                            },
                            modifier = Modifier.fillMaxWidth()
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(12.dp))
                }
            }
        }
    }
}

@Composable
fun PremiumOptionButton(
    option: Option,
    index: Int,
    isSelected: Boolean,
    isAnswerChecked: Boolean,
    onClick: () -> Unit
) {
    val labels = listOf("A", "B", "C", "D", "E")
    val label = labels.getOrElse(index) { "${index + 1}" }

    val defaultSurface = Color(0xFF1E293B).copy(alpha = 0.6f)
    val selectedSurface = Color(0xFF2563EB).copy(alpha = 0.2f)

    val bgColor = when {
        isAnswerChecked && option.isCorrect == 1 -> Color(0xFF10B981).copy(alpha = 0.15f)
        isAnswerChecked && isSelected && option.isCorrect == 0 -> Color(0xFFEF4444).copy(alpha = 0.15f)
        isSelected -> selectedSurface
        else -> defaultSurface
    }

    val borderColor = when {
        isAnswerChecked && option.isCorrect == 1 -> Color(0xFF10B981)
        isAnswerChecked && isSelected && option.isCorrect == 0 -> Color(0xFFEF4444)
        isSelected -> Color(0xFF2563EB)
        else -> Color.White.copy(alpha = 0.1f)
    }

    GlassyCard(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(enabled = !isAnswerChecked, onClick = onClick),
        shape = RoundedCornerShape(12.dp),
        containerColor = bgColor,
        borderColor = borderColor,
        borderWidth = if (isSelected || isAnswerChecked) 2.dp else 1.dp
    ) {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            modifier = Modifier.height(IntrinsicSize.Min)
        ) {
            Box(
                modifier = Modifier
                    .fillMaxHeight()
                    .width(42.dp)
                    .background(Color.White.copy(alpha = 0.05f)),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    text = label,
                    color = Color.White,
                    fontWeight = FontWeight.ExtraBold,
                    fontSize = 16.sp
                )
            }

            Box(
                modifier = Modifier
                    .weight(1f)
                    .padding(vertical = 12.dp, horizontal = 16.dp),
                contentAlignment = Alignment.CenterStart
            ) {
                Text(
                    text = option.optionText,
                    fontSize = 15.sp,
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    lineHeight = 20.sp
                )
                
                if (isAnswerChecked && option.isCorrect == 1) {
                    Icon(
                        imageVector = Icons.Default.Check,
                        contentDescription = null,
                        tint = Color(0xFF10B981),
                        modifier = Modifier.align(Alignment.CenterEnd)
                    )
                }
            }
        }
    }
}
