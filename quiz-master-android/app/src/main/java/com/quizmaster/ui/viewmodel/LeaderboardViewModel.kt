package com.quizmaster.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.quizmaster.data.model.RankingItem
import com.quizmaster.data.model.UserStats
import com.quizmaster.data.repository.LeaderboardRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class LeaderboardViewModel(private val repository: LeaderboardRepository) : ViewModel() {

    private val _leaderboard = MutableStateFlow<List<RankingItem>>(emptyList())
    val leaderboard: StateFlow<List<RankingItem>> = _leaderboard.asStateFlow()

    private val _userStats = MutableStateFlow<UserStats?>(null)
    val userStats: StateFlow<UserStats?> = _userStats.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error.asStateFlow()

    init {
        fetchLeaderboard()
    }

    fun fetchLeaderboard(limit: Int = 20) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            val result = repository.getLeaderboard(limit)
            _isLoading.value = false
            if (result.isSuccess) {
                _leaderboard.value = result.getOrDefault(emptyList())
            } else {
                _error.value = result.exceptionOrNull()?.message
            }
        }
    }

    fun fetchUserStats() {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            val result = repository.getUserStats()
            _isLoading.value = false
            if (result.isSuccess) {
                _userStats.value = result.getOrNull()
            } else {
                _error.value = result.exceptionOrNull()?.message
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}
