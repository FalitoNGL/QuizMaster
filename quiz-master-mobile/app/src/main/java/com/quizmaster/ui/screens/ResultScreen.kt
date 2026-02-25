package com.quizmaster.ui.screens

import androidx.compose.animation.core.*
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.Lightbulb
import androidx.compose.material.icons.filled.MenuBook
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.components.PremiumButton
import com.quizmaster.ui.components.PremiumLoadingScreen
import com.quizmaster.ui.theme.*
import com.quizmaster.data.model.Question
import com.quizmaster.ui.viewmodel.QuizViewModel

@Composable
fun ResultScreen(
    viewModel: QuizViewModel,
    onPlayAgain: () -> Unit,
    onGoHome: () -> Unit
) {
    val result = viewModel.submitResult.value
    val isLoading = viewModel.isLoading.value

    Box(
        modifier = Modifier
            .fillMaxSize(),
        contentAlignment = Alignment.Center
    ) {
        if (isLoading) {
            PremiumLoadingScreen(message = "Processing Results...")
        } else if (result != null) {
            if (result.accuracy >= 80) {
                ConfettiEffect()
            }
            // Use local scroll state for the result content to allow "Review Jawaban" to be at the bottom
            val scrollState = rememberScrollState()
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(scrollState)
                    .padding(24.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Performance Badge
                val (emoji, title, color) = when {
                    result.accuracy >= 80 -> Triple("🏆", "LEGENDARY!", Color(0xFFFFD700))
                    result.accuracy >= 60 -> Triple("⭐", "ELITE SKILLS!", LaravelBlue)
                    result.accuracy >= 40 -> Triple("👍", "GOOD WORK!", SuccessGreen)
                    else -> Triple("💪", "KEEP GRINDING!", ErrorRed)
                }

                val animatedScore by androidx.compose.animation.core.animateIntAsState(
                    targetValue = result.score,
                    animationSpec = androidx.compose.animation.core.tween(durationMillis = 2000, easing = androidx.compose.animation.core.FastOutSlowInEasing)
                )

                val animatedAccuracy by androidx.compose.animation.core.animateIntAsState(
                    targetValue = result.accuracy.toInt(),
                    animationSpec = androidx.compose.animation.core.tween(durationMillis = 2000, easing = androidx.compose.animation.core.FastOutSlowInEasing)
                )

                Box(
                    modifier = Modifier
                        .size(140.dp)
                        .clip(CircleShape)
                        .background(color.copy(alpha = 0.1f))
                        .border(4.dp, color.copy(alpha = 0.3f), CircleShape),
                    contentAlignment = Alignment.Center
                ) {
                    Text(text = emoji, fontSize = 64.sp)
                }

                Spacer(modifier = Modifier.height(24.dp))

                Text(
                    text = title,
                    fontSize = 32.sp,
                    fontWeight = FontWeight.Black,
                    color = Color.White,
                    letterSpacing = (-1).sp
                )

                Spacer(modifier = Modifier.height(40.dp))

                GlassyCard(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(32.dp)
                ) {
                    Column(
                        modifier = Modifier.fillMaxWidth().padding(8.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Text(
                            text = "TOTAL SCORE",
                            fontSize = 12.sp,
                            color = Color.White.copy(alpha = 0.5f),
                            letterSpacing = 2.sp,
                            fontWeight = FontWeight.Black
                        )

                        Text(
                            text = "$animatedScore",
                            fontSize = 72.sp,
                            fontWeight = FontWeight.Black,
                            color = color,
                            letterSpacing = (-2).sp
                        )

                        Spacer(modifier = Modifier.height(32.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceEvenly
                        ) {
                            ResultStatItem(
                                label = "CORRECT",
                                value = "${result.correct}/${result.total}",
                                color = Color.White
                            )
                            ResultStatItem(
                                label = "ACCURACY",
                                value = "$animatedAccuracy%",
                                color = Color.White
                            )
                        }
                    }
                }

                Spacer(modifier = Modifier.height(48.dp))

                PremiumButton(
                    text = "Battle Again ↻",
                    onClick = {
                        viewModel.resetQuiz()
                        onPlayAgain()
                    },
                    modifier = Modifier.fillMaxWidth()
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                OutlinedButton(
                    onClick = onGoHome,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(56.dp),
                    shape = RoundedCornerShape(16.dp), 
                    colors = ButtonDefaults.outlinedButtonColors(
                        contentColor = Color.White
                    ),
                    border = androidx.compose.foundation.BorderStroke(1.5.dp, Color.White.copy(alpha = 0.2f))
                ) {
                    Icon(Icons.Default.Home, contentDescription = null, modifier = Modifier.size(20.dp))
                    Spacer(modifier = Modifier.width(12.dp))
                    Text("Return Home", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                }

                Spacer(modifier = Modifier.height(48.dp))

                // Review Section Header
                Text(
                    text = "REVIEW JAWABAN",
                    color = Color.White,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Black,
                    letterSpacing = 2.sp,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Start
                )
                
                Spacer(modifier = Modifier.height(24.dp))

                val userAnswers = viewModel.getUserAnswers()
                val quizQuestions = viewModel.questions.value

                quizQuestions.forEachIndexed { index, question ->
                    val userAnswerId = userAnswers[question.id] as? Int
                    val isCorrect = question.options.find { it.id == userAnswerId }?.isCorrect == 1
                    
                    ReviewItem(
                        index = index,
                        question = question,
                        userAnswerId = userAnswerId,
                        isCorrect = isCorrect
                    )
                    Spacer(modifier = Modifier.height(16.dp))
                }
                
                Spacer(modifier = Modifier.height(32.dp))
            }
        }
    }
}

@Composable
fun ReviewItem(
    index: Int,
    question: com.quizmaster.data.model.Question,
    userAnswerId: Int?,
    isCorrect: Boolean
) {
    GlassyCard(
        modifier = Modifier.fillMaxWidth(),
        containerColor = Color.White.copy(alpha = 0.05f),
        shape = RoundedCornerShape(24.dp)
    ) {
        Column(modifier = Modifier.padding(4.dp)) {
            Row(verticalAlignment = Alignment.Top) {
                Box(
                    modifier = Modifier
                        .size(28.dp)
                        .clip(CircleShape)
                        .background(if (isCorrect) SuccessGreen.copy(alpha = 0.2f) else ErrorRed.copy(alpha = 0.2f))
                        .border(1.dp, if (isCorrect) SuccessGreen else ErrorRed, CircleShape),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "${index + 1}",
                        color = if (isCorrect) Color(0xFF10B981) else Color(0xFFEF4444),
                        fontWeight = FontWeight.Bold,
                        fontSize = 12.sp
                    )
                }
                Spacer(modifier = Modifier.width(12.dp))
                Text(
                    text = question.questionText,
                    color = Color.White,
                    fontSize = 16.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.weight(1f)
                )
            }
            
            Spacer(modifier = Modifier.height(16.dp))
            
            // Selected/Correct Info
            val selectedOptionText = question.options.find { it.id == userAnswerId }?.optionText ?: "Tidak Dijawab"
            val correctOptionText = question.options.find { it.isCorrect == 1 }?.optionText ?: "-"

            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .clip(RoundedCornerShape(16.dp))
                    .background(Color.White.copy(alpha = 0.03f))
                    .padding(12.dp)
            ) {
                Row {
                    Text("Jawaban Anda: ", color = Color.White.copy(alpha = 0.5f), fontSize = 13.sp)
                    Text(
                        text = selectedOptionText,
                        color = if (isCorrect) SuccessGreen else ErrorRed,
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
                if (!isCorrect) {
                    Spacer(modifier = Modifier.height(4.dp))
                    Row {
                        Text("Kunci Jawaban: ", color = Color.White.copy(alpha = 0.5f), fontSize = 13.sp)
                        Text(correctOptionText, color = SuccessGreen, fontSize = 13.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))

            Spacer(modifier = Modifier.height(12.dp))

            // Explanation (Redesigned per user feedback)
            if (!question.explanation.isNullOrBlank()) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clip(RoundedCornerShape(16.dp))
                        .background(
                            Brush.linearGradient(
                                colors = listOf(
                                    Color(0xFF3B82F6).copy(alpha = 0.15f), // Blue-ish tint
                                    Color(0xFF8B5CF6).copy(alpha = 0.05f)
                                )
                            )
                        )
                        .border(1.dp, Color.White.copy(alpha = 0.1f), RoundedCornerShape(16.dp))
                        .padding(16.dp)
                ) {
                    Column {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                imageVector = Icons.Default.Lightbulb,
                                contentDescription = null,
                                tint = Color(0xFFFBBF24), // Amber
                                modifier = Modifier.size(18.dp)
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Text(
                                text = "PEMBAHASAN",
                                color = Color(0xFFFBBF24),
                                fontWeight = FontWeight.Black,
                                fontSize = 12.sp,
                                letterSpacing = 1.sp
                            )
                        }

                        Spacer(modifier = Modifier.height(8.dp))

                        Text(
                            text = question.explanation,
                            color = Color.White.copy(alpha = 0.9f),
                            fontSize = 14.sp,
                            lineHeight = 20.sp
                        )

                        // Source / Reference
                        if (!question.reference.isNullOrBlank()) {
                            Spacer(modifier = Modifier.height(12.dp))
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Icon(
                                    imageVector = Icons.Default.MenuBook,
                                    contentDescription = null,
                                    tint = Color.White.copy(alpha = 0.5f),
                                    modifier = Modifier.size(14.dp)
                                )
                                Spacer(modifier = Modifier.width(6.dp))
                                Text(
                                    text = "Sumber: ${question.reference}",
                                    color = Color.White.copy(alpha = 0.5f),
                                    fontSize = 12.sp,
                                    fontStyle = androidx.compose.ui.text.font.FontStyle.Italic
                                )
                            }
                        }
                    }
                }
            } else {
                 Text(
                    text = "Tidak ada pembahasan.",
                    color = Color.White.copy(alpha = 0.5f),
                    fontSize = 12.sp,
                    fontStyle = androidx.compose.ui.text.font.FontStyle.Italic,
                    modifier = Modifier.padding(start = 4.dp)
                )
            }
        }
    }
}

@Composable
fun ResultStatItem(
    label: String,
    value: String,
    color: Color
) {
    Column(horizontalAlignment = Alignment.CenterHorizontally) {
        Text(
            text = value,
            fontSize = 24.sp,
            fontWeight = FontWeight.Black,
            color = color
        )
        Text(
            text = label,
            fontSize = 11.sp,
            color = Color.White.copy(alpha = 0.4f),
            fontWeight = FontWeight.ExtraBold,
            letterSpacing = 1.sp
        )
    }
}

@Composable
fun ConfettiEffect() {
    val colors = listOf(Color.Yellow, Color.Red, Color.Cyan, Color.Green, Color.Magenta, Color.Blue)
    val particles = remember { List(100) { ConfettiParticle(colors.random()) } }
    val infiniteTransition = rememberInfiniteTransition()
    
    val progress by infiniteTransition.animateFloat(
        initialValue = 0f,
        targetValue = 1f,
        animationSpec = infiniteRepeatable(
            animation = tween(4000, easing = LinearEasing),
            repeatMode = RepeatMode.Restart
        )
    )

    Canvas(modifier = Modifier.fillMaxSize()) {
        particles.forEach { particle ->
            val y = (particle.yStart + progress * size.height * particle.speed) % size.height
            val x = particle.xStart * size.width + (progress * 100 * particle.drift)
            drawCircle(
                color = particle.color,
                radius = 8f,
                center = Offset(x, y)
            )
        }
    }
}

class ConfettiParticle(val color: Color) {
    val xStart = (0..100).random() / 100f
    val yStart = (0..100).random() / 100f * -1000f
    val speed = (1..5).random() / 2f + 0.5f
    val drift = ((-50)..50).random() / 50f
}
