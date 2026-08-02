<template>
  <div class="min-h-screen bg-black">
    <!-- Video Layout -->
    <div class="video-layout">
      <!-- Main Video Area -->
      <div class="video-area">
        <!-- Remote Video (Main) - Shows the other participant -->
        <div class="main-video">
          <video ref="remoteVideo" autoplay playsinline class="video-element"></video>
          <div class="video-overlay">
            <div class="participant-info">
              <div class="participant-avatar" :class="{ placeholder: !remoteParticipant.avatar }">
                <img v-if="remoteParticipant.avatar" :src="remoteParticipant.avatar"
                  :alt="remoteParticipant.name || 'remote participant'" />
                <i v-else class="fas fa-user"></i>
              </div>
              <div>
                <p class="participant-name-text">
                  {{ remoteParticipant.name || (isTherapist ? 'المريض' : 'المعالج') }}
                </p>
                <p class="participant-role-text">
                  {{ isTherapist ? 'المريض' : 'المعالج' }}
                </p>
              </div>
            </div>
            <div class="video-status">
              <i class="fas fa-signal"></i>
            </div>
          </div>
        </div>

        <!-- Local Video (Picture-in-Picture) - Shows yourself -->
        <div class="pip-video">
          <video ref="localVideo" autoplay playsinline muted class="video-element local-video"></video>
          <div class="video-overlay">
            <div class="participant-info">
              <div class="participant-avatar" :class="{ placeholder: !localParticipant.avatar }">
                <img v-if="localParticipant.avatar" :src="localParticipant.avatar"
                  :alt="localParticipant.name || 'you'" />
                <i v-else class="fas fa-user"></i>
              </div>
              <div>
                <p class="participant-name-text">أنت</p>
                <p class="participant-role-text">
                  {{ isTherapist ? 'المعالج' : 'المريض' }}
                </p>
              </div>
            </div>
            <div class="video-status">
              <i class="fas fa-microphone" :class="{ 'text-red-500': !audioEnabled }"></i>
              <i class="fas fa-video" :class="{ 'text-red-500': !videoEnabled }"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Controls -->
      <div class="controls-bar">
        <button @click="toggleAudio" class="control-btn" :class="audioEnabled ? 'bg-gray-600' : 'bg-red-600'">
          <i class="fas" :class="audioEnabled ? 'fa-microphone' : 'fa-microphone-slash'"></i>
        </button>

        <button @click="toggleVideo" class="control-btn" :class="videoEnabled ? 'bg-gray-600' : 'bg-red-600'">
          <i class="fas" :class="videoEnabled ? 'fa-video' : 'fa-video-slash'"></i>
        </button>

        <button v-if="canEndSession" @click="endSession" class="control-btn bg-red-600 hover:bg-red-700">
          <i class="fas fa-phone-slash"></i>
        </button>
        <button v-else class="control-btn bg-gray-700 cursor-not-allowed opacity-60" title="لا يمكنك إنهاء الجلسة">
          <i class="fas fa-lock"></i>
        </button>

        <button @click="toggleChat" class="control-btn" :class="showChat ? 'bg-blue-600' : 'bg-gray-600'">
          <i class="fas fa-comment"></i>
        </button>
      </div>
    </div>

    <!-- Chat Panel -->
    <div v-if="showChat" class="chat-panel">
      <div class="chat-header">
        <h3 class="text-white">الدردشة</h3>
        <button @click="toggleChat" class="text-gray-400 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="chat-messages" ref="chatMessagesContainer">
        <div v-for="message in chatMessages" :key="message.id" class="message"
          :class="message.sender === 'therapist' ? 'message-therapist' : 'message-patient'">
          <div class="message-content">
            {{ message.text }}
          </div>
          <div class="message-time">{{ message.time }}</div>
        </div>
      </div>

      <div class="chat-input">
        <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="اكتب رسالتك..." class="message-input">
        <button type="button" @click="sendMessage" class="send-btn">
          <i class="fas fa-paper-plane"></i>
        </button>
      </div>
    </div>

    <!-- Session Info -->
    <div class="session-info">
      <div class="info-item">
        <i class="fas fa-clock"></i>
        <span>{{ timeRemaining }}</span>
      </div>
      <div class="info-item">
        <i :class="isTherapist ? 'fas fa-user' : 'fas fa-user-md'"></i>
        <span>
          <span v-if="isTherapist">{{ patientName || 'المريض' }}</span>
          <span v-else>{{ therapist.name || 'المعالج' }}</span>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { io } from 'socket.io-client'
import api from '@/utils/api'
import { useAuthStore } from '@/stores/auth'
import { useProfile } from '@/composables/useProfile'

export default {
  name: 'VideoSession',
  props: {
    roomId: {
      type: String,
      required: true
    }
  },
  setup() {
    const authStore = useAuthStore()
    const { user: profileUser } = useProfile()
    return { profileUser, authStore }
  },
  data() {
    return {
      session: null,
      loading: true,
      error: null,
      audioEnabled: true,
      videoEnabled: true,
      showChat: false,
      timeRemaining: '00:00',
      newMessage: '',
      therapist: {
        name: '',
        avatar: null
      },
      patientInfo: {
        name: '',
        avatar: null
      },
      localParticipant: {
        name: '',
        avatar: null
      },
      remoteParticipant: {
        name: '',
        avatar: null
      },
      patientName: '',
      isTherapist: false,
      currentUserId: null,
      currentUserName: '',
      currentUserRole: 'patient',
      chatMessages: [],
      localStream: null,
      remoteStream: null,
      peerConnection: null,
      socket: null,
      sessionTimer: null,
      startTime: null,
      signalingServerUrl: import.meta.env.VITE_SIGNALING_URL || 'http://localhost:3001',
      pendingIceCandidates: [],
      isConnected: false
    }
  },
  computed: {
    canEndSession() {
      const isAdmin = this.authStore?.user?.role === 'Admin'
      return this.isTherapist || Boolean(isAdmin)
    }
  },
  async mounted() {
    await this.loadSession()
    if (this.session) {
      await this.initializeWebRTC()
    }
  },
  beforeUnmount() {
    this.cleanup()
  },
  methods: {
    async loadSession() {
      try {
        this.loading = true
        const response = await api.get(`/sessions/room/${this.roomId}`)

        if (response.data) {
          this.session = response.data

          // تحديد نوع المستخدم
          const authStore = useAuthStore()
          const frontendToken = localStorage.getItem('frontend_token')
          const adminToken = localStorage.getItem('admin_token')

          let currentUser = null
          if (frontendToken && this.profileUser) {
            currentUser = this.profileUser
          } else if (adminToken && authStore.user) {
            currentUser = authStore.user
          } else {
            try {
              const userResponse = await api.get('/user')
              currentUser = userResponse.data?.data?.user || userResponse.data?.user
            } catch (error) {
              console.warn('Could not fetch user data')
            }
          }

          this.isTherapist = currentUser?.role === 'Therapist'
          this.currentUserRole = this.isTherapist ? 'therapist' : 'patient'
          this.currentUserId = currentUser?.id || null
          this.currentUserName = currentUser?.name || ''
          const currentUserAvatar = currentUser?.avatar || null

          const therapistName = this.session.appointment?.therapist?.name_ar ||
            this.session.appointment?.therapist?.name_en ||
            this.session.appointment?.therapist?.user?.name ||
            'المعالج'
          const therapistAvatar = this.session.appointment?.therapist?.user?.avatar || currentUserAvatar || null

          // بيانات المعالج
          this.therapist = {
            name: therapistName,
            avatar: therapistAvatar
          }

          // بيانات المريض
          const patientName = this.session.appointment?.client?.name ||
            this.session.appointment?.client?.name_ar ||
            'المريض'
          const patientAvatar = this.session.appointment?.client?.avatar || (!this.isTherapist ? currentUserAvatar : null)
          this.patientName = patientName
          this.patientInfo = {
            name: patientName,
            avatar: patientAvatar
          }

          // إعداد المشاركين المحليين والبعيدين
          if (this.isTherapist) {
            this.localParticipant = { name: this.currentUserName || therapistName || 'المعالج', avatar: currentUserAvatar || therapistAvatar }
            this.remoteParticipant = { name: patientName || 'المريض', avatar: patientAvatar }
          } else {
            this.localParticipant = { name: this.currentUserName || patientName || 'المريض', avatar: currentUserAvatar || patientAvatar }
            this.remoteParticipant = { name: therapistName || 'المعالج', avatar: therapistAvatar }
          }

          // Calculate remaining time
          if (this.session.start_time) {
            const start = new Date(this.session.start_time)
            const duration = this.session.appointment?.therapist?.session_duration || 45
            const endTime = new Date(start.getTime() + duration * 60000)
            this.startTime = endTime
            this.startSessionTimer()
          }
        }
      } catch (error) {
        console.error('Error loading session:', error)
        const status = error.response?.status
        const message = error.response?.data?.message
        if (status === 410) {
          alert(message || 'تم إنهاء هذه الجلسة ولا يمكن الانضمام إليها.')
          this.$router.push('/Session')
          return
        }
        this.error = 'فشل تحميل الجلسة. يرجى التحقق من الصلاحيات.'
        if (status === 403) {
          this.$router.push('/')
        }
      } finally {
        this.loading = false
      }
    },

    async initializeWebRTC() {
      try {
        // Get user media FIRST
        console.log('🎥 Requesting user media...')
        this.localStream = await navigator.mediaDevices.getUserMedia({
          video: { width: 1280, height: 720 },
          audio: true
        })

        console.log('✅ User media obtained')
        // Attach local stream immediately
        this.attachLocalStream()

        // Initialize Socket.IO connection BEFORE setting up peer connection
        console.log('🔌 Connecting to signaling server:', this.signalingServerUrl)
        this.socket = io(this.signalingServerUrl, {
          transports: ['websocket', 'polling'],
          reconnection: true,
          reconnectionAttempts: 10,
          reconnectionDelay: 1000,
          timeout: 20000
        })

        // Setup socket event listeners BEFORE joining room
        this.setupSocketListeners()

        // Setup WebRTC peer connection AFTER socket is ready
        this.setupPeerConnection()

        // الحصول على بيانات المستخدم من useProfile (للواجهة العامة) أو authStore (لللوحة التحكم)
        const authStore = useAuthStore()
        const frontendToken = localStorage.getItem('frontend_token')
        const adminToken = localStorage.getItem('admin_token')

        let userId = this.currentUserId || 0
        let userRole = this.currentUserRole || 'patient'

        if (!userId) {
          if (frontendToken && this.profileUser) {
            userId = this.profileUser.id || 0
            const role = this.profileUser.role || 'Client'
            userRole = role.toLowerCase() === 'therapist' ? 'therapist' : 'patient'
            this.currentUserRole = userRole
          } else if (adminToken && authStore.user) {
            userId = authStore.user.id || 0
            const role = authStore.user.role || 'Client'
            userRole = role.toLowerCase() === 'therapist' ? 'therapist' : 'patient'
            this.currentUserRole = userRole
          } else {
            try {
              const userResponse = await api.get('/user')
              const user = userResponse.data?.data?.user || userResponse.data?.user
              if (user) {
                userId = user.id || 0
                const role = user.role || 'Client'
                userRole = role.toLowerCase() === 'therapist' ? 'therapist' : 'patient'
                this.currentUserId = userId
                this.currentUserRole = userRole
              }
            } catch (error) {
              console.warn('Could not fetch user data, using defaults')
            }
          }
        } else {
          // إذا كان userId محدداً، تأكد من أن userRole صحيح
          if (this.isTherapist) {
            userRole = 'therapist'
            this.currentUserRole = 'therapist'
          } else {
            userRole = 'patient'
            this.currentUserRole = 'patient'
          }
        }

        console.log('🚪 Joining room with:', { roomId: this.roomId, userId, userRole })

        // Wait for socket to be connected before joining room
        const joinRoom = () => {
          console.log('🚪 Joining room:', { roomId: this.roomId, userId, userRole })
          this.socket.emit('joinRoom', {
            roomId: this.roomId,
            userId,
            userRole
          })
        }

        if (this.socket.connected) {
          joinRoom()
        } else {
          this.socket.once('connect', () => {
            console.log('✅ Socket connected, now joining room')
            joinRoom()
          })
        }

        // Send any pending ICE candidates once socket is connected
        this.socket.once('connect', () => {
          if (this.pendingIceCandidates && this.pendingIceCandidates.length > 0) {
            console.log(`📤 Sending ${this.pendingIceCandidates.length} pending ICE candidates`)
            this.pendingIceCandidates.forEach(candidate => {
              this.socket.emit('iceCandidate', {
                roomId: this.roomId,
                candidate: candidate
              })
            })
            this.pendingIceCandidates = []
          }
        })

      } catch (error) {
        console.error('❌ Error initializing WebRTC:', error)
        this.error = 'فشل تهيئة الجلسة. يرجى التحقق من الكاميرا والميكروفون.'
      }
    },

    setupPeerConnection() {
      // Close existing connection if any
      if (this.peerConnection) {
        this.peerConnection.close()
      }

      const configuration = {
        iceServers: [
          { urls: 'stun:stun.l.google.com:19302' },
          { urls: 'stun:stun1.l.google.com:19302' }
        ]
      }

      this.peerConnection = new RTCPeerConnection(configuration)

      // Add local stream tracks immediately
      if (this.localStream) {
        this.localStream.getTracks().forEach(track => {
          console.log(`➕ Adding ${track.kind} track to peer connection`)
          this.peerConnection.addTrack(track, this.localStream)
        })
      } else {
        console.warn('⚠️ No local stream available when setting up peer connection')
      }

      // Handle remote stream
      this.peerConnection.ontrack = (event) => {
        console.log('📹 Received remote track:', event.track.kind, event.track.id)
        
        // Initialize remote stream if needed
        if (!this.remoteStream) {
          this.remoteStream = new MediaStream()
        }

        // Add track to remote stream
        if (event.streams && event.streams[0]) {
          // If we have a stream, use it
          event.streams[0].getTracks().forEach(track => {
            if (!this.remoteStream.getTracks().find(t => t.id === track.id)) {
              this.remoteStream.addTrack(track)
              console.log(`✅ Added ${track.kind} track from stream to remote stream`)
            }
          })
        } else if (event.track) {
          // Handle single track
          if (!this.remoteStream.getTracks().find(t => t.id === event.track.id)) {
            this.remoteStream.addTrack(event.track)
            console.log(`✅ Added ${event.track.kind} track to remote stream`)
          }
        }

        // Attach stream to video element
        this.attachRemoteStream()

        // Log stream state
        console.log(`📊 Remote stream now has ${this.remoteStream.getTracks().length} tracks`)
      }

      // Handle ICE candidates
      this.peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
          console.log('🧊 Sending ICE candidate:', event.candidate.type)
          if (this.socket && this.socket.connected) {
            this.socket.emit('iceCandidate', {
              roomId: this.roomId,
              candidate: event.candidate
            })
          } else {
            console.warn('⚠️ Socket not connected, storing ICE candidate')
            // Store candidate to send later
            if (!this.pendingIceCandidates) {
              this.pendingIceCandidates = []
            }
            this.pendingIceCandidates.push(event.candidate)
          }
        } else {
          console.log('🧊 ICE gathering complete')
        }
      }

      // Handle ICE gathering state
      this.peerConnection.onicegatheringstatechange = () => {
        console.log('🧊 ICE gathering state:', this.peerConnection.iceGatheringState)
      }

      // Handle connection state changes
      this.peerConnection.onconnectionstatechange = () => {
        const state = this.peerConnection.connectionState
        console.log('🔗 Connection state:', state)

        if (state === 'connected') {
          console.log('✅ WebRTC connection established!')
          this.error = null
        } else if (state === 'disconnected' || state === 'failed') {
          console.warn('⚠️ WebRTC connection lost')
          this.error = 'انقطع الاتصال. جاري إعادة المحاولة...'
        } else if (state === 'connecting') {
          console.log('🔄 Connecting...')
        }
      }

      // Handle ICE connection state
      this.peerConnection.oniceconnectionstatechange = () => {
        const state = this.peerConnection.iceConnectionState
        console.log('🧊 ICE connection state:', state)
      }
    },

    async createOffer() {
      try {
        console.log('📤 Creating offer...')
        const offer = await this.peerConnection.createOffer({
          offerToReceiveAudio: true,
          offerToReceiveVideo: true
        })
        await this.peerConnection.setLocalDescription(offer)

        console.log('📤 Sending offer to room:', this.roomId)
        this.socket.emit('offer', {
          roomId: this.roomId,
          offer: offer
        })
      } catch (error) {
        console.error('❌ Error creating offer:', error)
      }
    },

    setupSocketListeners() {
      // Socket connection events
      this.socket.on('connect', () => {
        console.log('✅ Connected to signaling server')
        this.isConnected = true
        this.error = null
      })

      this.socket.on('disconnect', () => {
        console.warn('⚠️ Disconnected from signaling server')
        this.isConnected = false
        this.error = 'انقطع الاتصال بخادم الإشارة. جاري إعادة الاتصال...'
      })

      this.socket.on('connect_error', (error) => {
        console.error('❌ Connection error:', error)
        this.error = 'فشل الاتصال بخادم الإشارة. تأكد من أن الخادم يعمل على ' + this.signalingServerUrl
      })

      this.socket.on('joinedRoom', (data) => {
        console.log('✅ Joined room:', data)
      })

      this.socket.on('existingParticipants', async (data) => {
        console.log('👥 Existing participants:', data)
        // إذا كان هناك مشاركون موجودون، أنشئ offer فوراً
        if (data.count > 0 && this.peerConnection) {
          console.log(`📤 Creating offer for ${data.count} existing participants`)
          setTimeout(async () => {
            await this.createOffer()
          }, 300)
        }
      })

      this.socket.on('userJoined', async (data) => {
        console.log('👤 User joined:', data)
        // Create offer when another user joins (after ensuring peer connection is ready)
        if (this.peerConnection && this.peerConnection.signalingState === 'stable') {
          setTimeout(async () => {
            await this.createOffer()
          }, 300)
        } else {
          // Wait for peer connection to be ready
          const checkReady = setInterval(() => {
            if (this.peerConnection && this.peerConnection.signalingState === 'stable') {
              clearInterval(checkReady)
              setTimeout(async () => {
                await this.createOffer()
              }, 300)
            }
          }, 100)
          setTimeout(() => clearInterval(checkReady), 5000)
        }
      })

      this.socket.on('offer', async (data) => {
        console.log('📥 Received offer:', data)
        try {
          // إذا كان peer connection في حالة غير مستقرة، انتظر قليلاً
          if (this.peerConnection.signalingState !== 'stable') {
            console.log('⚠️ Signaling state not stable, waiting...', this.peerConnection.signalingState)
            await new Promise(resolve => setTimeout(resolve, 100))
          }

          // Set remote description
          await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer))
          console.log('✅ Remote description set')

          // Create and set local answer
          const answer = await this.peerConnection.createAnswer({
            offerToReceiveAudio: true,
            offerToReceiveVideo: true
          })
          await this.peerConnection.setLocalDescription(answer)
          console.log('✅ Local description set (answer)')

          console.log('📤 Sending answer to:', data.fromSocketId)
          this.socket.emit('answer', {
            roomId: this.roomId,
            answer: answer,
            targetSocketId: data.fromSocketId
          })
        } catch (error) {
          console.error('❌ Error handling offer:', error)
        }
      })

      this.socket.on('answer', async (data) => {
        console.log('📥 Received answer:', data)
        try {
          // إذا كان الاتصال في حالة مستقرة ولديه وصف عن بعد، نتجاهل الإجابة المكررة
          if (this.peerConnection.signalingState === 'stable' && this.peerConnection.currentRemoteDescription) {
            console.warn('Skipping duplicate answer in stable state')
            return
          }
          // Set remote description (answer)
          await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer))
          console.log('✅ Remote description set (answer)')
        } catch (error) {
          console.error('❌ Error handling answer:', error)
        }
      })

      this.socket.on('iceCandidate', async (data) => {
        console.log('🧊 Received ICE candidate:', data.candidate?.type)
        try {
          if (!data?.candidate) {
            console.warn('Received empty ICE candidate payload, skipping')
            return
          }
          const candidate = new RTCIceCandidate(data.candidate)

          if (this.peerConnection.remoteDescription) {
            await this.peerConnection.addIceCandidate(candidate)
            console.log('✅ ICE candidate added')
          } else {
            // إذا لم يكن remote description جاهزاً، احفظ المرشح
            console.log('⏳ Remote description not ready, storing candidate')
            if (!this.pendingIceCandidates) {
              this.pendingIceCandidates = []
            }
            this.pendingIceCandidates.push(candidate)

            // Process pending candidates when remote description is set
            const processPending = async () => {
              if (this.peerConnection.remoteDescription && this.pendingIceCandidates?.length > 0) {
                console.log('📦 Processing', this.pendingIceCandidates.length, 'pending ICE candidates')
                const candidates = [...this.pendingIceCandidates]
                this.pendingIceCandidates = []
                for (const cand of candidates) {
                  try {
                    await this.peerConnection.addIceCandidate(cand)
                  } catch (error) {
                    console.warn('Failed to add pending candidate:', error)
                  }
                }
              }
            }

            // Check periodically for remote description
            const checkInterval = setInterval(() => {
              if (this.peerConnection.remoteDescription) {
                processPending()
                clearInterval(checkInterval)
              }
            }, 100)

            // Clear after 5 seconds if still not ready
            setTimeout(() => clearInterval(checkInterval), 5000)
          }
        } catch (error) {
          console.error('❌ Error adding ICE candidate:', error)
        }
      })

      this.socket.on('userLeft', (data) => {
        console.log('User left:', data)
        // Handle user leaving
      })

      this.socket.on('error', (error) => {
        console.error('Socket error:', error)
        this.error = error.message || 'حدث خطأ في الاتصال'
      })

      this.socket.on('chatMessage', (message) => {
        console.log('📥 Raw chat message received:', message)
        
        if (!message || !message.text) {
          console.warn('Received invalid chat message:', message)
          return
        }

        // التحقق من عدم وجود الرسالة مسبقاً (بناءً على ID أو النص والمرسل)
        const messageId = message.id || `remote-${message.senderSocketId || 'user'}-${Date.now()}`
        const messageText = message.text.trim()
        const messageSender = message.sender || message.senderRole || 'patient'
        const messageTime = message.time || new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })

        // التحقق من الرسائل المكررة (نفس ID أو نفس النص من نفس المرسل خلال ثانية)
        const existingMessage = this.chatMessages.find(m =>
          (m.id === messageId) || 
          (m.text === messageText && m.sender === messageSender && Math.abs(new Date(m.time) - new Date(messageTime)) < 1000)
        )

        if (existingMessage) {
          console.log('Message already exists, skipping:', messageId)
          return
        }

        const formattedMessage = {
          id: messageId,
          sender: messageSender,
          text: messageText,
          time: messageTime
        }

        console.log('✅ Adding chat message from', messageSender + ':', formattedMessage)
        this.chatMessages.push(formattedMessage)
        this.scrollChatToBottom()
      })

      // الاستماع لحدث إنهاء الجلسة
      this.socket.on('sessionEnded', (data) => {
        console.log('🔴 Session ended event received:', data)
        alert('تم إنهاء الجلسة من قبل المشرف.')
        this.cleanup()
        this.$router.push('/Session')
      })
    },

    attachLocalStream() {
      if (!this.localStream) return

      this.$nextTick(() => {
        const localVideo = this.$refs.localVideo
        if (localVideo) {
          if (localVideo.srcObject !== this.localStream) {
            localVideo.srcObject = this.localStream
          }
          localVideo.muted = true
          localVideo.play().catch(err => {
            console.warn('Local video auto-play prevented:', err)
          })
          console.log('✅ Local stream attached to PIP video')
        }
      })
    },

    attachRemoteStream() {
      if (!this.remoteStream) {
        console.warn('⚠️ No remote stream to attach')
        return
      }

      this.$nextTick(() => {
        const remoteVideo = this.$refs.remoteVideo
        if (remoteVideo) {
          // Check if stream has tracks
          if (this.remoteStream.getTracks().length === 0) {
            console.warn('⚠️ Remote stream has no tracks')
            return
          }

          console.log(`📹 Attaching remote stream with ${this.remoteStream.getTracks().length} tracks`)
          if (remoteVideo.srcObject !== this.remoteStream) {
            remoteVideo.srcObject = this.remoteStream
          }
          
          // Force play
          remoteVideo.muted = false
          remoteVideo.play().then(() => {
            console.log('✅ Remote video playing')
          }).catch(err => {
            console.warn('❌ Remote video auto-play prevented:', err)
            // Try again with user interaction
            remoteVideo.play().catch(e => console.error('Failed to play remote video:', e))
          })
        } else {
          console.warn('⚠️ Remote video element not found')
        }
      })
    },

    toggleAudio() {
      this.audioEnabled = !this.audioEnabled
      if (this.localStream) {
        this.localStream.getAudioTracks().forEach(track => {
          track.enabled = this.audioEnabled
        })
      }
    },

    toggleVideo() {
      this.videoEnabled = !this.videoEnabled
      if (this.localStream) {
        this.localStream.getVideoTracks().forEach(track => {
          track.enabled = this.videoEnabled
        })
      }
    },

    toggleChat() {
      this.showChat = !this.showChat
      if (this.showChat) {
        this.scrollChatToBottom()
      }
    },

    sendMessage() {
      const trimmed = this.newMessage.trim()
      if (!trimmed) {
        return
      }

      // التحقق من اتصال السوكت
      if (!this.socket) {
        console.error('❌ Socket not initialized')
        alert('الاتصال غير متاح. يرجى إعادة تحميل الصفحة.')
        return
      }

      // التأكد من أن السوكت متصل
      if (!this.socket.connected) {
        console.warn('⚠️ Socket not connected yet, waiting...')
        const messageText = trimmed // حفظ النص قبل الانتظار
        this.socket.once('connect', () => {
          console.log('✅ Socket connected, sending saved message')
          this.newMessage = messageText // استعادة النص
          this.sendMessage() // إعادة المحاولة
        })
        return
      }

      // تحديد نوع المرسل بشكل صحيح
      const senderRole = this.isTherapist ? 'therapist' : 'patient'
      const messageId = `local-${this.currentUserId || 'user'}-${Date.now()}`

      const message = {
        id: messageId,
        sender: senderRole,
        senderRole: senderRole,
        senderId: this.currentUserId || 0,
        text: trimmed,
        time: new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }),
        isLocal: true // علامة للرسائل المحلية
      }

      // إضافة الرسالة محلياً أولاً (لإظهارها فوراً)
      this.chatMessages.push(message)
      this.scrollChatToBottom()

      // إرسال الرسالة عبر السوكت
      console.log('📤 Sending chat message:', { roomId: this.roomId, message })
      this.socket.emit('chatMessage', {
        roomId: this.roomId,
        message: {
          ...message,
          isLocal: false // إزالة العلامة عند الإرسال
        }
      })

      this.newMessage = ''
    },

    scrollChatToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.chatMessagesContainer
        if (container) {
          container.scrollTop = container.scrollHeight
        }
      })
    },

    startSessionTimer() {
      if (!this.startTime) return

      // التأكد من أن startTime هو Date object
      const endTime = this.startTime instanceof Date ? this.startTime : new Date(this.startTime)

      this.sessionTimer = setInterval(() => {
        const now = new Date()
        const diff = endTime.getTime() - now.getTime()

        if (diff <= 0) {
          clearInterval(this.sessionTimer)
          this.timeRemaining = '00:00'
          if (this.canEndSession) {
            this.endSession(true)
          } else {
            alert('انتهى وقت الجلسة. سيتم إعادتك إلى صفحة الجلسات.')
            this.cleanup()
            this.$router.push('/Session')
          }
          return
        }

        const minutes = Math.floor(diff / 60000)
        const seconds = Math.floor((diff % 60000) / 1000)

        this.timeRemaining = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
      }, 1000)
    },

    async endSession(skipConfirm = false) {
      if (!this.canEndSession) {
        alert('لا يمكنك إنهاء الجلسة. هذا الإجراء متاح للمعالج أو المشرف فقط.')
        return
      }

      if (!skipConfirm) {
        const confirmed = confirm('هل تريد إنهاء الجلسة؟')
        if (!confirmed) {
          return
        }
      }

      try {
        if (this.session?.id) {
          await api.post(`/sessions/${this.session.id}/end`)
          this.session.status = 'ended'
        }
        alert('تم إنهاء الجلسة بنجاح.')
      } catch (error) {
        console.error('Error ending session:', error)
        const message = error.response?.data?.message || 'فشل إنهاء الجلسة.'
        alert(message)
        return
      }

      this.cleanup()
      this.$router.push('/Session')
    },

    cleanup() {
      // Stop timer
      if (this.sessionTimer) {
        clearInterval(this.sessionTimer)
      }

      // Close socket connection
      if (this.socket) {
        this.socket.emit('leaveRoom', { roomId: this.roomId })
        this.socket.disconnect()
      }

      // Close peer connection
      if (this.peerConnection) {
        this.peerConnection.close()
      }

      // Stop local stream
      if (this.localStream) {
        this.localStream.getTracks().forEach(track => track.stop())
      }
    }
  }
}
</script>

<style scoped>
.video-layout {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.video-area {
  flex: 1;
  position: relative;
  background: #000;
}

.main-video {
  width: 100%;
  height: 100%;
  position: relative;
}

.pip-video {
  position: absolute;
  top: 1rem;
  left: 1rem;
  width: 200px;
  height: 150px;
  border: 2px solid white;
  border-radius: 8px;
  overflow: hidden;
  background: #333;
}

.local-video {
  transform: scaleX(-1);
}

.video-element {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.video-overlay {
  position: absolute;
  bottom: 0;
  right: 0;
  left: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
  padding: 1rem;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.participant-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.participant-avatar {
  width: 44px;
  height: 44px;
  border-radius: 9999px;
  overflow: hidden;
  border: 2px solid rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
}

.participant-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.participant-avatar.placeholder i {
  color: #fff;
  font-size: 1rem;
}

.participant-name-text {
  font-weight: 600;
  color: #fff;
  margin-bottom: 0.1rem;
}

.participant-role-text {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.75);
  margin: 0;
}

.video-status {
  display: flex;
  gap: 0.5rem;
}

.controls-bar {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.8);
}

.control-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: none;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.control-btn:hover {
  transform: scale(1.1);
}

.chat-panel {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: 350px;
  background: white;
  display: flex;
  flex-direction: column;
  border-left: 1px solid #e2e8f0;
}

.chat-header {
  padding: 1rem;
  background: #1f2937;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-messages {
  flex: 1;
  padding: 1rem;
  overflow-y: auto;
  background: #f8fafc;
}

.message {
  margin-bottom: 1rem;
}

.message-patient {
  text-align: left;
}

.message-therapist {
  text-align: right;
}

.message-content {
  padding: 0.75rem;
  border-radius: 1rem;
  display: inline-block;
  max-width: 80%;
}

.message-patient .message-content {
  background: #3b82f6;
  color: white;
  border-bottom-right-radius: 0.25rem;
}

.message-therapist .message-content {
  background: #e5e7eb;
  color: #374151;
  border-bottom-left-radius: 0.25rem;
}

.message-time {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.chat-input {
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 0.5rem;
}

.message-input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  outline: none;
}

.message-input:focus {
  border-color: #3b82f6;
}

.send-btn {
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  padding: 0 1rem;
  cursor: pointer;
}

.send-btn:hover {
  background: #2563eb;
}

.session-info {
  position: fixed;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.info-item {
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
  .pip-video {
    width: 120px;
    height: 90px;
  }

  .chat-panel {
    width: 100%;
  }

  .controls-bar {
    padding: 0.5rem;
  }

  .control-btn {
    width: 45px;
    height: 45px;
  }
}
</style>