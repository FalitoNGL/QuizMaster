package com.quizmaster.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.quizmaster.data.model.User
import com.quizmaster.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class AuthViewModel(private val repository: AuthRepository) : ViewModel() {

    private val _user = MutableStateFlow<User?>(null)
    val user: StateFlow<User?> = _user.asStateFlow()

    private val _isAuthenticated = MutableStateFlow(false)
    val isAuthenticated: StateFlow<Boolean> = _isAuthenticated.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error.asStateFlow()

    init {
        checkAuthStatus()
    }

    private fun checkAuthStatus() {
        viewModelScope.launch {
            repository.authToken.collect { token ->
                _isAuthenticated.value = !token.isNullOrEmpty()
                if (!token.isNullOrEmpty()) {
                    fetchUserProfile()
                }
            }
        }
    }

    private fun fetchUserProfile() {
        viewModelScope.launch {
            val result = repository.getUserProfile()
            if (result.isSuccess) {
                _user.value = result.getOrNull()
            }
        }
    }

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            val result = repository.login(email, password)
            _isLoading.value = false
            if (result.isSuccess) {
                _user.value = result.getOrNull()
                _isAuthenticated.value = true
            } else {
                _error.value = result.exceptionOrNull()?.message
            }
        }
    }

    fun register(name: String, email: String, password: String) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            val result = repository.register(name, email, password)
            _isLoading.value = false
            if (result.isSuccess) {
                _user.value = result.getOrNull()
                _isAuthenticated.value = true
            } else {
                _error.value = result.exceptionOrNull()?.message
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            repository.logout()
            _user.value = null
            _isAuthenticated.value = false
        }
    }

    fun clearError() {
        _error.value = null
    }
}
