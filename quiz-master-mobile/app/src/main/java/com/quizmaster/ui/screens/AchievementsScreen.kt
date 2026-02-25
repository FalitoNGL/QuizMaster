package com.quizmaster.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.itemsIndexed
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Stars
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.SolidColor
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AchievementsScreen(
    onBackClick: () -> Unit
) {
    Box(
        modifier = Modifier.fillMaxSize()
    ) {
        Column(modifier = Modifier.fillMaxSize()) {
            CenterAlignedTopAppBar(
                title = {
                    Text(
                        "Achievements",
                        color = Color.White,
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 20.sp
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(
                            Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = Color.White
                        )
                    }
                },
                colors = TopAppBarDefaults.centerAlignedTopAppBarColors(
                    containerColor = Color.Transparent
                )
            )

            LazyVerticalGrid(
                columns = GridCells.Fixed(2),
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 24.dp),
                contentPadding = PaddingValues(top = 16.dp, bottom = 100.dp),
                horizontalArrangement = Arrangement.spacedBy(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                item(span = { androidx.compose.foundation.lazy.grid.GridItemSpan(2) }) {
                    AchievementHeader()
                }

                itemsIndexed(dummyAchievements) { index, medal ->
                    AchievementMedalCard(medal)
                }
            }
        }
    }
}

@Composable
fun AchievementHeader() {
    GlassyCard(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(24.dp),
        containerColor = Color(0xFF1a2035)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(8.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(60.dp)
                    .clip(CircleShape)
                    .background(Brush.linearGradient(listOf(Color(0xFFeab308), Color(0xFFf97316)))),
                contentAlignment = Alignment.Center
            ) {
                Icon(Icons.Default.Stars, contentDescription = null, tint = Color.White, modifier = Modifier.size(32.dp))
            }
            Spacer(modifier = Modifier.width(16.dp))
            Column {
                Text(
                    "Pusat Koleksi",
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp
                )
                Text(
                    "12 dari 45 medal terkumpul",
                    color = Color.White.copy(alpha = 0.5f),
                    fontSize = 12.sp
                )
            }
        }
    }
}

@Composable
fun AchievementMedalCard(achievement: AchievementItem) {
    val isLocked = achievement.isLocked
    
    GlassyCard(
        modifier = Modifier.fillMaxWidth().height(180.dp),
        shape = RoundedCornerShape(20.dp),
        containerColor = if (isLocked) Color(0xFF0d1117) else Color(0xFF1a2035),
        borderColor = if (isLocked) Color.White.copy(alpha = 0.05f) else Color.White.copy(alpha = 0.1f)
    ) {
        Column(
            modifier = Modifier.fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Box(
                modifier = Modifier
                    .size(64.dp)
                    .clip(CircleShape)
                    .background(
                        if (isLocked) SolidColor(Color.White.copy(alpha = 0.05f))
                        else Brush.verticalGradient(listOf(achievement.color, achievement.color.copy(alpha = 0.6f)))
                    ),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = if (isLocked) Icons.Default.Lock else achievement.icon,
                    contentDescription = null,
                    tint = if (isLocked) Color.White.copy(alpha = 0.2f) else Color.White,
                    modifier = Modifier.size(32.dp)
                )
            }
            
            Spacer(modifier = Modifier.height(12.dp))
            
            Text(
                text = achievement.name,
                color = if (isLocked) Color.White.copy(alpha = 0.3f) else Color.White,
                fontWeight = FontWeight.Bold,
                fontSize = 14.sp,
                textAlign = androidx.compose.ui.text.style.TextAlign.Center
            )
            
            Text(
                text = achievement.description,
                color = Color.White.copy(alpha = if (isLocked) 0.1f else 0.4f),
                fontSize = 10.sp,
                textAlign = androidx.compose.ui.text.style.TextAlign.Center,
                modifier = Modifier.padding(start = 8.dp, end = 8.dp, top = 4.dp)
            )
        }
    }
}

data class AchievementItem(
    val name: String,
    val description: String,
    val icon: ImageVector,
    val color: Color,
    val isLocked: Boolean = false
)

val dummyAchievements = listOf(
    AchievementItem("First Flight", "Menyelesaikan kuis pertama", Icons.Default.EmojiEvents, Color(0xFF6366f1)),
    AchievementItem("Perfect Score", "Mendapatkan akurasi 100%", Icons.Default.EmojiEvents, Color(0xFFeab308)),
    AchievementItem("Fast Learner", "Level up 5 kali sepekan", Icons.Default.EmojiEvents, Color(0xFF22c55e)),
    AchievementItem("Quiz Master", "Menyelesaikan 100 kuis", Icons.Default.EmojiEvents, Color(0xFFec4899), true),
    AchievementItem("Daily Hero", "Login 7 hari berturut", Icons.Default.EmojiEvents, Color(0xFFef4444), true),
    AchievementItem("Night Owl", "Main di atas jam 12 malam", Icons.Default.EmojiEvents, Color(0xFF8b5cf6), true)
)
