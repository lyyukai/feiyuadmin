/**
 * AI Store - Manages AI chat state
 * FeiyuAdmin v3
 */
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAiStore = defineStore('ai', () => {
  // ---- State ----
  const sessions = ref([])
  const currentSessionId = ref(null)
  const messages = ref([])
  const aiModels = ref([])
  const activePrompts = ref([])
  const sendingMessage = ref(false)
  const loading = ref(false)
  const promptsLoading = ref(false)

  // ---- Getters ----
  const currentSession = computed(() =>
    sessions.value.find(s => s.id === currentSessionId.value)
  )

  const sortedSessions = computed(() =>
    [...sessions.value].sort((a, b) => {
      const ta = a.updatedAt ? new Date(a.updatedAt).getTime() : 0
      const tb = b.updatedAt ? new Date(b.updatedAt).getTime() : 0
      return tb - ta
    })
  )

  // ---- Actions ----
  async function fetchAiModels() {
    // Mock data — replace with real API call
    aiModels.value = [
      { id: 'gpt-4', name: 'GPT-4' },
      { id: 'gpt-3.5', name: 'GPT-3.5' },
      { id: 'claude-3', name: 'Claude-3' },
    ]
  }

  async function fetchSessions() {
    loading.value = true
    try {
      // Mock — replace with real API
      sessions.value = []
    } finally {
      loading.value = false
    }
  }

  async function createSession(name = '') {
    const id = `session_${Date.now()}`
    const session = {
      id,
      name: name || `新对话 ${sessions.value.length + 1}`,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }
    sessions.value.unshift(session)
    currentSessionId.value = id
    messages.value = []
    return session
  }

  async function deleteSession(sessionId) {
    const idx = sessions.value.findIndex(s => s.id === sessionId)
    if (idx !== -1) sessions.value.splice(idx, 1)
    if (currentSessionId.value === sessionId) {
      currentSessionId.value = sessions.value[0]?.id || null
      messages.value = []
    }
  }

  async function renameSession(sessionId, name) {
    const session = sessions.value.find(s => s.id === sessionId)
    if (session) {
      session.name = name
      session.updatedAt = new Date().toISOString()
    }
  }

  async function fetchMessages(sessionId) {
    // Mock — replace with real API
  }

  async function sendMessage(content, sessionId) {
    sendingMessage.value = true
    const userMsg = {
      id: `msg_${Date.now()}`,
      role: 'user',
      content,
      createdAt: new Date().toISOString(),
      loading: false,
    }
    messages.value.push(userMsg)

    // Ensure session exists
    if (!currentSessionId.value) {
      await createSession()
    }

    try {
      // Simulate AI response — replace with real API call
      await new Promise(r => setTimeout(r, 1000))
      const aiMsg = {
        id: `msg_${Date.now()}_ai`,
        role: 'ai',
        content: `这是AI的回复：${content}`,
        createdAt: new Date().toISOString(),
        loading: false,
        type: 'text',
      }
      messages.value.push(aiMsg)
    } finally {
      sendingMessage.value = false
    }
  }

  async function fetchPrompts() {
    promptsLoading.value = true
    try {
      // Mock — replace with real API
      activePrompts.value = [
        { id: 1, name: '代码助手', description: '辅助写代码', userPromptTemplate: '' },
        { id: 2, name: 'SQL转换', description: '自然语言转SQL', userPromptTemplate: '' },
      ]
    } finally {
      promptsLoading.value = false
    }
  }

  function clearMessages() {
    messages.value = []
  }

  return {
    sessions,
    currentSessionId,
    messages,
    aiModels,
    activePrompts,
    sendingMessage,
    loading,
    promptsLoading,
    currentSession,
    sortedSessions,
    fetchAiModels,
    fetchSessions,
    createSession,
    deleteSession,
    renameSession,
    fetchMessages,
    sendMessage,
    fetchPrompts,
    clearMessages,
  }
})
