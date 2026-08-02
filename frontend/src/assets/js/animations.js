// animations.js - حركات الظهور والاختفاء للمقاطع
export const useScrollAnimation = {
  data() {
    return {
      isVisible: false,
      scrollObserver: null,
      lastScrollY: 0,
      isScrollingDown: false
    }
  },
  computed: {
    sectionClass() {
      return {
        'opacity-0': !this.isVisible,
        'opacity-100': this.isVisible
      }
    },
    titleClass() {
      return {
        'opacity-0 translate-y-8': !this.isVisible,
        'opacity-100 translate-y-0': this.isVisible
      }
    },
    lineClass() {
      return {
        'opacity-0 scale-x-0': !this.isVisible,
        'opacity-100 scale-x-100': this.isVisible
      }
    },
    contentItemClass() {
      return {
        'opacity-0 translate-x-8': !this.isVisible,
        'opacity-100 translate-x-0': this.isVisible
      }
    },
    imageClass() {
      return {
        'opacity-0 translate-x-8': !this.isVisible,
        'opacity-100 translate-x-0': this.isVisible
      }
    },
    buttonClass() {
      return {
        'opacity-0 translate-y-4': !this.isVisible,
        'opacity-100 translate-y-0': this.isVisible
      }
    }
  },
  mounted() {
    this.trackScrollDirection()
    this.setupScrollObserver()
  },
  beforeUnmount() {
    if (this.scrollObserver) {
      this.scrollObserver.disconnect()
    }
    window.removeEventListener('scroll', this.handleScroll)
  },
  methods: {
    trackScrollDirection() {
      this.lastScrollY = window.scrollY
      window.addEventListener('scroll', this.handleScroll, { passive: true })
    },
    
    handleScroll() {
      const currentScrollY = window.scrollY
      this.isScrollingDown = currentScrollY > this.lastScrollY
      this.lastScrollY = currentScrollY
    },
    
    setupScrollObserver() {
      this.scrollObserver = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              // عندما يدخل العنصر منطقة الرؤية - نظهره
              this.isVisible = true
            } else {
              // عندما يخرج العنصر من منطقة الرؤية
              if (!this.isScrollingDown) {
                // إذا كنا نصعد - نخفي العنصر عندما يمر منه 30%
                const elementHeight = entry.boundingClientRect.height
                const viewportHeight = window.innerHeight
                const elementTop = entry.boundingClientRect.top
                
                // حساب نسبة الخروج من الأعلى
                const exitRatio = Math.abs(elementTop) / elementHeight
                
                if (exitRatio >= 0.3) {
                  this.isVisible = false
                }
              }
            }
          })
        },
        {

          threshold: 0.3,

          rootMargin: '0px 0px -10% 0px'
        }
      )

      const sectionRef = this.$refs.whySection || this.$refs.sectionRef || Object.values(this.$refs)[0]
      
      if (sectionRef) {
        this.scrollObserver.observe(sectionRef)
      }
    }
  }
}