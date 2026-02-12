package com.quizmaster.ui.viewmodel

import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.State
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.quizmaster.data.api.RetrofitClient
import com.quizmaster.data.model.*
import kotlinx.coroutines.launch

class QuizViewModel : ViewModel() {

    // State untuk kategori
    private val _categories = mutableStateOf<List<Category>>(emptyList())
    val categories: State<List<Category>> = _categories

    // State untuk soal kuis aktif
    private val _questions = mutableStateOf<List<Question>>(emptyList())
    val questions: State<List<Question>> = _questions

    // State untuk leaderboard
    private val _leaderboard = mutableStateOf<List<LeaderboardEntry>>(emptyList())
    val leaderboard: State<List<LeaderboardEntry>> = _leaderboard

    // State loading & error
    private val _isLoading = mutableStateOf(false)
    val isLoading: State<Boolean> = _isLoading

    private val _error = mutableStateOf<String?>(null)
    val error: State<String?> = _error

    // State untuk quiz aktif
    private val _currentCategory = mutableStateOf<Category?>(null)
    val currentCategory: State<Category?> = _currentCategory

    private val _quizLimit = mutableStateOf(10)
    val quizLimit: State<Int> = _quizLimit

    private val _quizTimer = mutableStateOf(30)
    val quizTimer: State<Int> = _quizTimer

    private val _currentQuestionIndex = mutableStateOf(0)
    val currentQuestionIndex: State<Int> = _currentQuestionIndex

    // State untuk hasil
    private val _submitResult = mutableStateOf<SubmitResponse?>(null)
    val submitResult: State<SubmitResponse?> = _submitResult

    // State untuk streak
    private val _streak = mutableStateOf(0)
    val streak: State<Int> = _streak

    // State untuk skor live
    private val _currentScore = mutableStateOf(0)
    val currentScore: State<Int> = _currentScore

    // Jawaban user (question_id -> answer)
    private val userAnswers = mutableMapOf<Int, Any?>()
    private val timesLeft = mutableMapOf<Int, Int>()

    // Player name
    var playerName = "Player"

    /**
     * Load semua kategori
     */
    fun loadCategories() {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            try {
                val response = RetrofitClient.apiService.getCategories()
                if (response.isSuccessful && response.body()?.success == true) {
                    _categories.value = response.body()?.data ?: emptyList()
                } else {
                    _error.value = "Gagal memuat kategori"
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "Terjadi kesalahan"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Load soal berdasarkan kategori
     */
    fun loadQuiz(category: Category, limit: Int = 10, timer: Int = 30) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            _currentCategory.value = category
            _quizLimit.value = limit
            _quizTimer.value = timer
            _currentQuestionIndex.value = 0
            _streak.value = 0 // Reset Streak
            _currentScore.value = 0 // Reset Skor
            userAnswers.clear()
            timesLeft.clear()

            try {
                val response = RetrofitClient.apiService.getQuiz(category.id, limit)
                if (response.isSuccessful && response.body()?.success == true) {
                    _questions.value = response.body()?.questions ?: emptyList()
                } else {
                    _error.value = "Gagal memuat soal"
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "Terjadi kesalahan"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    // ... (saveAnswer is here) ...

    // ... (removed duplicate resetQuiz and streak declaration)

    /**
     * Simpan jawaban user untuk soal tertentu dan update streak
     * Mengembalikan true jika jawaban benar (untuk feedback instan)
     */
    fun saveAnswer(questionId: Int, answer: Any?, timeLeft: Int): Boolean {
        var isCorrect = false
        if (userAnswers[questionId] != answer) {
            userAnswers[questionId] = answer
            timesLeft[questionId] = timeLeft

            val currentQ = _questions.value.find { it.id == questionId }
            if (currentQ != null) {
                // Single Choice
                if (currentQ.type == "single" && answer is Int) {
                    val selectedOption = currentQ.options.find { it.id == answer }
                    isCorrect = selectedOption?.isCorrect == 1
                }
                
                if (isCorrect) {
                     _streak.value++
                     _currentScore.value += (100 + timeLeft)
                } else {
                     _streak.value = 0
                }
            }
        } else {
            // Jika memilih jawaban yang sama, kita tetap cek status kebenarannya
            val currentQ = _questions.value.find { it.id == questionId }
            if (currentQ != null && currentQ.type == "single" && answer is Int) {
                val selectedOption = currentQ.options.find { it.id == answer }
                isCorrect = selectedOption?.isCorrect == 1
            }
        }
        return isCorrect
    }

    /**
     * Pindah ke soal berikutnya
     */
    fun nextQuestion() {
        if (_currentQuestionIndex.value < _questions.value.size - 1) {
            _currentQuestionIndex.value++
        }
    }

    /**
     * Pindah ke soal sebelumnya
     */
    fun previousQuestion() {
        if (_currentQuestionIndex.value > 0) {
            _currentQuestionIndex.value--
        }
    }

    /**
     * Submit semua jawaban ke server
     */
    fun submitQuiz() {
        val category = _currentCategory.value ?: return

        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null

            val answerItems = userAnswers.map { (qId, ans) ->
                AnswerItem(
                    questionId = qId,
                    answer = ans,
                    timeLeft = timesLeft[qId] ?: 0
                )
            }

            val request = SubmitRequest(
                playerName = playerName,
                categoryId = category.id,
                answers = answerItems
            )

            try {
                val response = RetrofitClient.apiService.submitQuiz(request)
                if (response.isSuccessful && response.body()?.success == true) {
                    _submitResult.value = response.body()
                } else {
                    _error.value = "Gagal mengirim jawaban"
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "Terjadi kesalahan"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Load leaderboard
     */
    fun loadLeaderboard() {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val response = RetrofitClient.apiService.getLeaderboard()
                if (response.isSuccessful && response.body()?.success == true) {
                    _leaderboard.value = response.body()?.data ?: emptyList()
                }
            } catch (e: Exception) {
                _error.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Get user answers for review
     */
    fun getUserAnswers(): Map<Int, Any?> = userAnswers.toMap()
    
    /**
     * Get time left for each question
     */
    fun getUserTimeLeft(): Map<Int, Int> = timesLeft.toMap()

    /**
     * Check if a question was answered correctly
     * Returns: 1 (Correct), -1 (Wrong), 0 (Not Answered)
     */
    fun getQuestionStatus(index: Int): Int {
        val question = _questions.value.getOrNull(index) ?: return 0
        val answer = userAnswers[question.id] ?: return 0
        
        if (question.type == "single" && answer is Int) {
            val selectedOption = question.options.find { it.id == answer }
            return if (selectedOption?.isCorrect == 1) 1 else -1
        }
        return 0
    }

    /**
     * Reset state untuk mulai kuis baru
     */
    fun resetQuiz() {
        _questions.value = emptyList()
        _currentQuestionIndex.value = 0
        _currentScore.value = 0
        _submitResult.value = null
        userAnswers.clear()
        timesLeft.clear()
    }
}
