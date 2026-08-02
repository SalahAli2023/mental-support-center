
export const measuresData = [
    // ========== مقياس القلق  ==========
    {
      id: 1,
      title: {
        ar: 'استبيان الصحة النفسية الشامل',
        en: 'Comprehensive Mental Health Survey'
      },
      description: {
        ar: 'استبيان شامل يتضمن أنواع متعددة من الأسئلة لتقييم الصحة النفسية',
        en: 'Comprehensive survey with multiple question types to assess mental health'
      },
      category: 'mental_health',
      questions: [
        {
          type: 'yes_no',
          text: {
            ar: 'هل تشعر بالرضا عن حياتك بشكل عام؟',
            en: 'Are you generally satisfied with your life?'
          },
          required: true
        },
        {
          type: 'multiple_choice',
          text: {
            ar: 'كم مرة تمارس الرياضة في الأسبوع؟',
            en: 'How often do you exercise per week?'
          },
          options: [
            { ar: 'أبداً', en: 'Never', value: 0 },
            { ar: 'مرة واحدة', en: 'Once', value: 1 },
            { ar: '2-3 مرات', en: '2-3 times', value: 2 },
            { ar: '4-5 مرات', en: '4-5 times', value: 3 },
            { ar: 'يومياً', en: 'Daily', value: 4 }
          ],
          required: true
        },
        {
          type: 'linear_scale',
          text: {
            ar: 'ما هو مستوى توترك في العمل؟',
            en: 'What is your stress level at work?'
          },
          scaleFrom: 1,
          scaleTo: 5,
          scaleLabels: {
            low: { ar: 'منخفض جداً', en: 'Very Low' },
            high: { ar: 'مرتفع جداً', en: 'Very High' }
          },
          required: true
        },
        {
          type: 'checkboxes',
          text: {
            ar: 'ما هي مصادر الدعم التي تعتمد عليها؟ (اختر جميع ما ينطبق)',
            en: 'What support sources do you rely on? (Select all that apply)'
          },
          options: [
            { ar: 'العائلة', en: 'Family', value: 1 },
            { ar: 'الأصدقاء', en: 'Friends', value: 2 },
            { ar: 'زملاء العمل', en: 'Colleagues', value: 3 },
            { ar: 'أخصائي نفسي', en: 'Psychologist', value: 4 },
            { ar: 'مجموعات الدعم', en: 'Support groups', value: 5 }
          ]
        },
        {
          type: 'short_answer',
          text: {
            ar: 'ما هو أكبر تحدي تواجهه حالياً؟',
            en: 'What is your biggest challenge currently?'
          }
        },
        {
          type: 'dropdown',
          text: {
            ar: 'ما هو مستوى نومك الجيد؟',
            en: 'What is your quality of sleep?'
          },
          options: [
            { ar: 'ممتاز', en: 'Excellent', value: 5 },
            { ar: 'جيد جداً', en: 'Very Good', value: 4 },
            { ar: 'جيد', en: 'Good', value: 3 },
            { ar: 'متوسط', en: 'Average', value: 2 },
            { ar: 'ضعيف', en: 'Poor', value: 1 }
          ],
          required: true
        },
        {
          type: 'paragraph',
          text: {
            ar: 'صف شعورك تجاه علاقاتك الاجتماعية:',
            en: 'Describe your feelings about your social relationships:'
          }
        },
        {
          type: 'yes_no',
          text: {
            ar: 'هل تشعر أنك تتلقى الدعم الكافي من محيطك؟',
            en: 'Do you feel you receive adequate support from your environment?'
          }
        },
        {
          type: 'multiple_choice',
          text: {
            ar: 'كيف تقيم صحتك النفسية الحالية؟',
            en: 'How would you rate your current mental health?'
          },
          options: [
            { ar: 'ممتازة', en: 'Excellent', value: 5 },
            { ar: 'جيدة جداً', en: 'Very Good', value: 4 },
            { ar: 'جيدة', en: 'Good', value: 3 },
            { ar: 'متوسطة', en: 'Average', value: 2 },
            { ar: 'ضعيفة', en: 'Poor', value: 1 }
          ],
          required: true
        }
      ],
      time: '15 دقائق',
      scoring: {
        interpretation: [
          { min: 0, max: 15, level: { ar: 'ممتاز', en: 'Excellent' }, desc: { ar: 'صحتك النفسية ممتازة', en: 'Your mental health is excellent' } },
          { min: 16, max: 25, level: { ar: 'جيد', en: 'Good' }, desc: { ar: 'صحتك النفسية جيدة بشكل عام', en: 'Your mental health is generally good' } },
          { min: 26, max: 35, level: { ar: 'متوسط', en: 'Average' }, desc: { ar: 'هناك مجال للتحسين في صحتك النفسية', en: 'There is room for improvement in your mental health' } }
        ]
      }
    },



    //     id: 'anxiety',
    //     title: { ar: 'مقياس القلق', en: 'Anxiety Scale' },
    //     description: { 
    //         ar: 'مقياس معتمد عالمياً لتقييم أعراض القلق العام وشدتها خلال الأسبوعين الماضيين',
    //         en: 'Internationally certified scale to assess general anxiety symptoms and their severity over the past two weeks'
    //     },
    //     category: 'women',
    //     icon: 'fas fa-heartbeat',
    //     questions: 7,
    //     time: '3-5',
    //     rating: 4,
    //     reviews: 128,
    //     questions: [
    //         { ar: "خلال الأسبوعين الماضيين، كم مرة شعرت بالتوتر أو القلق أو التوتر الشديد؟", en: "Over the last two weeks, how often have you felt nervous, anxious, or on edge?" },
    //         { ar: "كم مرة وجدت صعوبة في التوقف عن القلق أو السيطرة عليه؟", en: "How often have you been unable to stop or control worrying?" },
    //         { ar: "كم مرة قلقك تسبب في صعوبة في التركيز على ما تفعله؟", en: "How often have you had trouble relaxing?" },
    //         { ar: "كم مرة شعرت بصعوبة في الاسترخاء؟", en: "How often have you been so restless that it's hard to sit still?" },
    //         { ar: "كم مرة شعرت بعدم الراحة لدرجة أنك تجلس بهدوء كان صعباً؟", en: "How often have you become easily annoyed or irritable?" },
    //         { ar: "كم مرة أصبحت منزعجاً أو غاضباً بسهولة؟", en: "How often have you felt afraid as if something awful might happen?" },
    //         { ar: "كم مرة شعرت بالخوف كما لو أن شيئاً فظيعاً قد يحدث؟", en: "How often have you felt afraid as if something awful might happen?" }
    //     ],
    //     options: [
    //         { ar: "أبداً", en: "Not at all" },
    //         { ar: "عدة أيام", en: "Several days" },
    //         { ar: "أكثر من نصف الأيام", en: "More than half the days" },
    //         { ar: "تقريباً كل يوم", en: "Nearly every day" }
    //     ],
    //     scores: [0, 1, 2, 3],
    //     interpretation: (score, lang) => {
    //         const results = {
    //             ar: [
    //                 { level: 'قلق طفيف أو معدوم', desc: 'مستوى قلقك ضمن المعدل الطبيعي. هذا مؤشر جيد على صحتك النفسية.' },
    //                 { level: 'قلق بسيط', desc: 'تعاني من مستوى بسيط من القلق. قد يكون مفيداً تعلم تقنيات إدارة القلق.' },
    //                 { level: 'قلق متوسط', desc: 'القلق المتوسط قد يؤثر على جودة حياتك. ننصح بمناقشة النتائج مع أخصائي نفسي.' },
    //                 { level: 'قلق شديد', desc: 'مستوى القلق شديد ويحتاج إلى تدخل فوري. ننصح بمراجعة أخصائي نفسي بأسرع وقت.' }
    //             ],
    //             en: [
    //                 { level: 'Minimal or No Anxiety', desc: 'Your anxiety level is within the normal range. This is a good indicator of your mental health.' },
    //                 { level: 'Mild Anxiety', desc: 'You experience mild anxiety. It might be helpful to learn anxiety management techniques.' },
    //                 { level: 'Moderate Anxiety', desc: 'Moderate anxiety may affect your quality of life. We recommend discussing results with a psychologist.' },
    //                 { level: 'Severe Anxiety', desc: 'Anxiety level is severe and requires immediate intervention. We recommend seeing a psychologist as soon as possible.' }
    //             ]
    //         };
            
    //         if (score <= 4) return results[lang][0];
    //         if (score <= 9) return results[lang][1];
    //         if (score <= 14) return results[lang][2];
    //         return results[lang][3];
    //     }
    // },
    // ========== مقياس تايلور للقلق ==========
    
    {
        id: 'taylor-anxiety',
        title: { ar: 'مقياس تايلور للقلق', en: 'Taylor Anxiety Scale' },
        description: { 
            ar: 'مقياس متكامل لتقييم مستوى القلق من خلال 50 بنداً تغطي مختلف أعراض القلق',
            en: 'Comprehensive scale to assess anxiety level through 50 items covering various anxiety symptoms'
        },
        category: 'women',
        icon: 'fas fa-brain',
        questions: 50,
        time: '10-15',
        rating: 4,
        reviews: 87,
        questions: [
            { ar: "نومي مضطرب ومتقطع", en: "My sleep is disturbed and intermittent" },
            { ar: "مخاوفي كثيرة جدًا مقارنة بأصدقائي", en: "I have many more fears compared to my friends" },
            { ar: "يمر بي أيام لا أنام بسبب القلق", en: "I go through days without sleeping due to anxiety" },
            { ar: "أعتقد أنني أكثر عصبية من الآخرين", en: "I think I am more nervous than others" },
            { ar: "أعاني كل عدة ليالي من كوابيس مزعجة", en: "I suffer from disturbing nightmares every few nights" },
            { ar: "أعاني من الألآم بالمعدة في كثير من الأحيان", en: "I often suffer from stomach pains" },
            { ar: "كثير ما ألاحظ أن يداي ترتعش عندما أقوم بأي عمل", en: "I often notice my hands shaking when I do any work" },
            { ar: "كثيرًا ما أعاني من إسهال", en: "I often suffer from diarrhea" },
            { ar: "تثير قلقي أمور العمل والمال", en: "Work and money matters worry me" },
            { ar: "تصيبني نوبات من الغثيان", en: "I get bouts of nausea" },
            { ar: "أخشى أن يحمر وجهي خجلًا", en: "I am afraid my face will turn red with embarrassment" },
            { ar: "دائمًا ما أشعر بالجوع", en: "I always feel hungry" },
            { ar: "لا أثق في نفسي", en: "I don't trust myself" },
            { ar: "أتعب بسهولة", en: "I get tired easily" },
            { ar: "الانتظار يجعلني عصبي جدًا", en: "Waiting makes me very nervous" },
            { ar: "كثيرًا ما أشعر بالتوتر لدرجة أننا أعجز عن النوم", en: "I often feel so tense that I am unable to sleep" },
            { ar: "عادة لا أكون هادئا، وأي شيء يستثيرني", en: "I am usually not calm, and anything excites me" },
            { ar: "تمر بي فترات من التوتر لا أستطيع الجلوس طويلًا", en: "I go through periods of tension where I cannot sit for long" },
            { ar: "أنا غير سعيد في كل الأوقات", en: "I am unhappy all the time" },
            { ar: "يصعب عليا التركيز أثناء أداء العمل", en: "I find it difficult to concentrate while working" },
            { ar: "دائمًا ما أشعر بالقلق دون مبرر", en: "I always feel anxious without reason" },
            { ar: "عندما أشاهد مشاجرة أبتعد عنها", en: "When I see a fight, I stay away from it" },
            { ar: "أتمنى أن كون سعيدًا مثل الآخرين", en: "I wish I could be happy like others" },
            { ar: "دائمًا ما ينتابني شعور بالقلق على أشياء غامضة", en: "I always feel anxious about vague things" },
            { ar: "أشعر بأني عديم الفائدة", en: "I feel useless" },
            { ar: "كثيرًا ما أشعر بأني سوف أنفجر من الضيق والضجر", en: "I often feel like I will explode from distress and boredom" },
            { ar: "أعرق كثيرًا وبسهولة، حتى في الأيام الباردة", en: "I sweat a lot and easily, even on cold days" },
            { ar: "الحياة بالنسبة لي تعب ومضايقات", en: "Life for me is fatigue and annoyances" },
            { ar: "أجد نفسي دائمًا مشغول، وأخاف من المجهول", en: "I always find myself busy, and I fear the unknown" },
            { ar: "بالعادة أشعر بالخجل من نفسي", en: "I usually feel ashamed of myself" },
            { ar: "كثيرًا ما أشعر بأن قلبي يخفق بسرعة", en: "I often feel my heart beating fast" },
            { ar: "أبكي بسهولة", en: "I cry easily" },
            { ar: "خشيت أشياء وأشخاص لا يمكنهم إيذائي", en: "I fear things and people who cannot harm me" },
            { ar: "أتأثر كثيرًا بالأحداث", en: "I am greatly affected by events" },
            { ar: "أعاني كثيرًا من الصداع", en: "I suffer a lot from headaches" },
            { ar: "أشعر بالقلق على أمور وأشياء لا قيمة لها", en: "I feel anxious about matters and things that are worthless" },
            { ar: "لا أستطيع التركيز في شيء واحد", en: "I cannot concentrate on one thing" },
            { ar: "ارتبك بسهولة عندما أقوم بعمل أي شيء", en: "I get confused easily when I do anything" },
            { ar: "أشعر بأني عديم الفائدة، وأعتقد أحيانًا أني لا اصلح لشيء", en: "I feel useless, and sometimes I think I am good for nothing" },
            { ar: "أنا شخص متوتر جدًا", en: "I am a very tense person" },
            { ar: "أحيانًا عندما ارتبك أعرق، ويسقط العرق مني بصورة تضايقني", en: "Sometimes when I get confused, I sweat, and the sweat falls off me in a way that bothers me" },
            { ar: "يحمر وجهي خجلًا عندما أتحدث مع الآخرين", en: "My face turns red with embarrassment when I talk to others" },
            { ar: "أنا حساس أكثر من الآخرين", en: "I am more sensitive than others" },
            { ar: "مرت بي أوقات عصيبة لم أستطع التغلب عليها", en: "I have gone through difficult times that I could not overcome" },
            { ar: "أشعر بالتوتر عندما أصحى من نومي بالعادة", en: "I feel tense when I wake up from sleep usually" },
            { ar: "يداي وقدماي باردتان في العادة", en: "My hands and feet are usually cold" },
            { ar: "غالبًا ما أحلم بأشياء من الأفضل الا أخبر أحد بها", en: "I often dream of things that I better not tell anyone about" },
            { ar: "تنقصني الثقة بالنفس", en: "I lack self-confidence" },
            { ar: "قليلًا ما يحصل لي حالات إمساك تضايقني", en: "I rarely get constipated in a way that bothers me" },
            { ar: "يحمر وجهي من الخجل", en: "My face turns red from shame" }
        ],
        options: [
            { ar: "لا", en: "No" },
            { ar: "نعم", en: "Yes" }
        ],
        scores: [0, 1], // لا = 0, نعم = 1
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'خال من القلق', desc: 'مستوى القلق لديك ضمن المعدل الطبيعي. هذا مؤشر ممتاز على صحتك النفسية.' },
                    { level: 'قلق بسيط', desc: 'هناك مؤشرات على وجود قلق بسيط. قد تحتاج لمتابعة حالتك وتعلم تقنيات الاسترخاء.' },
                    { level: 'قلق نوعًا ما', desc: 'هناك مستوى متوسط من القلق يؤثر على حياتك. ننصح بمناقشة النتائج مع أخصائي.' },
                    { level: 'قلق شديد', desc: 'مستوى القلق شديد ويحتاج لاهتمام فوري. ننصح بمراجعة أخصائي نفسي.' },
                    { level: 'قلق شديد جدًا', desc: 'مستوى القلق شديد جداً ويتطلب تدخلاً فورياً. ننصح بمراجعة أخصائي نفسي بأسرع وقت.' }
                ],
                en: [
                    { level: 'Free from Anxiety', desc: 'Your anxiety level is within the normal range. This is an excellent indicator of your mental health.' },
                    { level: 'Mild Anxiety', desc: 'There are indicators of mild anxiety. You may need to monitor your condition and learn relaxation techniques.' },
                    { level: 'Moderate Anxiety', desc: 'There is a moderate level of anxiety affecting your life. We recommend discussing results with a specialist.' },
                    { level: 'Severe Anxiety', desc: 'Anxiety level is severe and requires immediate attention. We recommend seeing a psychologist.' },
                    { level: 'Very Severe Anxiety', desc: 'Anxiety level is very severe and requires immediate intervention. We recommend seeing a psychologist as soon as possible.' }
                ]
            };
            
            if (score <= 16) return results[lang][0];
            if (score <= 20) return results[lang][1];
            if (score <= 26) return results[lang][2];
            if (score <= 29) return results[lang][3];
            return results[lang][4];
        }
    },

    // ========== مقياس SCL-90-R (قائمة مراجعة الأعراض) ==========
    {
        id: 'scl-90-r',
        title: { ar: 'مقياس الصحة النفسية SCL-90-R', en: 'SCL-90-R Mental Health Scale' },
        description: { 
            ar: 'مقياس شامل لتقييم 9 اضطرابات نفسية أساسية من خلال 90 بنداً',
            en: 'Comprehensive scale to assess 9 basic psychological disorders through 90 items'
        },
        category: 'women',
        icon: 'fas fa-clipboard-list',
        questions: 90,
        time: '15-20',
        rating: 4,
        reviews: 112,
        questions: [
            { ar: "لدي صداع مستمر", en: "I have constant headaches", category: "S" },
            { ar: "أشعر بالإعياء أو الأغماء أو الدوخة", en: "I feel faint, dizzy, or weak", category: "S" },
            { ar: "أشعر بآلام في القلب والصدر", en: "I feel pains in my heart and chest", category: "S" },
            { ar: "أشعر بآلام في أسفل الظهر", en: "I feel pains in my lower back", category: "S" },
            { ar: "أشعر بغيثان واضطراب في المعدة", en: "I feel nausea and stomach upset", category: "S" },
            { ar: "أشعر بآلام في العضلات", en: " I feel muscle pains", category: "S" },
            { ar: "أجد صعوبة في التقاط انفاسي", en: "I have difficulty catching my breath", category: "S" },
            { ar: "أشعر بنوبات من السخونة أو البرودة في جسمي", en: "I feel hot or cold flashes in my body", category: "S" },
            { ar: "أشعر تنميل أو خدران في أجزاء من جسمي", en: "I feel numbness or tingling in parts of my body", category: "S" },
            { ar: "أشعر بأن شيء يقف في حلقي ( غصة )", en: "I feel like something is stuck in my throat", category: "S" },
            { ar: "أشعر بضعف في أجزاء من جسمي", en: "I feel weakness in parts of my body", category: "S" },
            { ar: "أشعر بثقل في اليدين أو القدمين", en: "I feel heaviness in my hands or feet", category: "S" },
            { ar: "لدي أفكار أو خواطر أو ألفاظ غير مرغوب بها لا تفارق بالي", en: "I have unwanted thoughts, ideas, or words that don't leave my mind", category: "OCD" },
            { ar: "لدي صعوبة في تذكر الأشياء", en: "I have difficulty remembering things", category: "OCD" },
            { ar: "لدي انشغال زائد فيما يتعلق بالقذارة والإهمال", en: "I am overly concerned with dirt and negligence", category: "OCD" },
            { ar: "لا امتلك القدرة على إتمام أعمالي", en: "I don't have the ability to complete my tasks", category: "OCD" },
            { ar: "اضطر إلى أداء أعمالي ببطء شديد حتى أتأكد من دقتها", en: "I have to do my work very slowly to ensure its accuracy", category: "OCD" },
            { ar: "اضطر إلى إعادة التأكد من أفعالي ( تعيد وتزيد )", en: "I have to repeatedly check my actions", category: "OCD" },
            { ar: "أجد صعوبة في اتخاذ القرارات", en: "I have difficulty making decisions", category: "OCD" },
            { ar: "أشعر بأن ذهني خالي من الأفكار", en: "I feel my mind is empty of thoughts", category: "OCD" },
            { ar: "أعاني من صعوبة في التركيز", en: "I have difficulty concentrating", category: "OCD" },
            { ar: "اضطر إلى تكرار نفس الأفعال كاللمس والعد والغسيل ( تكرار قهري )", en: "I have to repeat the same actions like touching, counting, and washing", category: "OCD" },
            { ar: "أشعر بالحساسية تجاه الأخرين", en: "I feel sensitive towards others", category: "IN" },
            { ar: "أشعر بالخجل أو الاضطراب مع الجنس الأخر", en: "I feel shy or disturbed with the opposite sex", category: "IN" },
            { ar: "أحس بأن مشاعري يمكن أن تجرح بسهولة", en: "I feel my feelings can be easily hurt", category: "IN" },
            { ar: "أشعر بأن الأخرين لا يفهمونني ولا يتعاطفون معي", en: "I feel others don't understand me or sympathize with me", category: "IN" },
            { ar: "أشعر بعدم مصداقية الناس لي أو أنهم لا يحبوني", en: "I feel people are not sincere with me or don't like me", category: "IN" },
            { ar: "أشعر بأني أقل من الأخرين ( شعور بالنقص )", en: "I feel inferior to others", category: "IN" },
            { ar: "أشعر بالاضطراب والضيق عندما يتحدث الناس عني أو يراقبوني", en: "I feel disturbed and upset when people talk about me or watch me", category: "IN" },
            { ar: "أشعر بالخجل والهيبة في وجود الأخرين", en: "I feel shy and in awe in the presence of others", category: "IN" },
            { ar: "أشعر بالضيق عند تناول الطعام أو الشراب في مكان عام", en: "I feel upset when eating or drinking in public", category: "IN" },
            { ar: "فقدت الاهتمام الجنسي أو اللذة الجنسية", en: "I have lost sexual interest or pleasure", category: "D" },
            { ar: "أشعر بالخمول أو قلة النشاط", en: "I feel lethargic or have low activity", category: "D" },
            { ar: "تراودني أفكار في إنهاء حياتي", en: "I have thoughts about ending my life", category: "D" },
            { ar: "أبكي بسهولة", en: "I cry easily", category: "D" },
            { ar: "أشعر بأنني محبوس أو مقيد الحركة", en: "I feel trapped or restricted in movement", category: "D" },
            { ar: "آلوم نفسي على الأحداث التي مررت بها", en: "I blame myself for the events I went through", category: "D" },
            { ar: "أشعر بالوحدة", en: "I feel lonely", category: "D" },
            { ar: "أشعر بالانقباض", en: "I feel constricted", category: "D" },
            { ar: "أقلق على الأشياء بصورة مبالغ فيها", en: "I worry about things excessively", category: "D" },
            { ar: "فقدت الاهتمام بمن حولي", en: "I have lost interest in those around me", category: "D" },
            { ar: "أشعر باليأس من المستقبل", en: "I feel hopeless about the future", category: "D" },
            { ar: "أشعر بأن كل شيء عناء في عناء ( الدنيا تعب في تعب )", en: "I feel everything is hardship upon hardship", category: "D" },
            { ar: "أشعر بأنني عديم الأهمية", en: "I feel I am worthless", category: "D" },
            { ar: "أعاني من ضعف الشهية للطعام", en: "I suffer from poor appetite", category: "D" },
            { ar: "أجد صعوبة بالنوم", en: "I have difficulty sleeping", category: "D" },
            { ar: "أفكر في الموت", en: "I think about death", category: "D" },
            { ar: "لدي زيادة شهية للطعام", en: "I have increased appetite", category: "D" },
            { ar: "استيقظ من النوم في الساعات المبكرة من الصباح", en: "I wake up early in the morning hours", category: "D" },
            { ar: "نومي مضطرب أو غير مريح", en: "My sleep is disturbed or uncomfortable", category: "D" },
            { ar: "أشعر بالذنب", en: "I feel guilty", category: "D" },
            { ar: "أعاني من الانفعال أو الاضطراب الداخلي", en: "I suffer from agitation or internal disturbance", category: "A" },
            { ar: "أشعر برعشة في جسمي", en: "I feel tremors in my body", category: "A" },
            { ar: "ينتابني رعب مفاجئ وبدون سبب", en: "I experience sudden terror without reason", category: "A" },
            { ar: "أشعر بالخوف", en: "I feel fear", category: "A" },
            { ar: "أشعر بزيادة ضربات القلب", en: "I feel increased heartbeats", category: "A" },
            { ar: "أشعر بالتوتر أو أنني مشدود داخليا", en: "I feel tense or internally tight", category: "A" },
            { ar: "أشعر بنوبات من الفزع أو الذعر بدون سبب معقول", en: "I experience panic or terror attacks without reasonable cause", category: "A" },
            { ar: "أشعر بعدم الاستقرار لدرجة أنني لا أستطيع الجلوس هادئا في مكانا ما", en: "I feel so unstable that I can't sit quietly in one place", category: "A" },
            { ar: "أشعر بأن الأشياء المألوفة تبدو غريبة أو غير حقيقية أو كأنها خيال أو حلم", en: "I feel familiar things seem strange, unreal, or like fantasy or dream", category: "A" },
            { ar: "لدي اعتقاد بأنني مدفوع لعمل أشياء معينة ( إحساس بدافع للعمل قهرا )", en: "I believe I am driven to do certain things", category: "A" },
            { ar: "أشعر بسرعة المضايقة والاستثارة", en: "I feel easily annoyed and excited", category: "H" },
            { ar: "تنتابني ثورات مزاجية لا يمكنني السيطرة عليها", en: "I experience mood swings I cannot control", category: "H" },
            { ar: "أشعر بدافع ملح بأن اضرب أو اجرح أو أؤذي شخص معين", en: "I feel an urgent urge to hit, injure, or harm someone", category: "H" },
            { ar: "أشعر بدافع ملح في تكسير أو تخريب الأشياء", en: "I feel an urgent urge to break or destroy things", category: "H" },
            { ar: "أدخل في كثير من الجدل والمناقشات", en: "I get into many arguments and discussions", category: "H" },
            { ar: "تنتابني نوبات من الصراخ وقذف الأشياء", en: "I experience episodes of screaming and throwing things", category: "H" },
            { ar: "أشعر بالخوف من الأماكن المفتوحة أو الشوارع", en: "I feel fear of open places or streets", category: "PH" },
            { ar: "أشعر بالخوف إذا خرجت من المنزل بمفردي", en: "I feel fear if I leave home alone", category: "PH" },
            { ar: "أشعر بالخوف عند السفر بالسيارات العامة ووسائل النقل", en: "I feel fear when traveling by public cars and transportation", category: "PH" },
            { ar: "اضطر إلى تجنب أشياء أو أفعال أو أماكن معينة لأنها تسبب لي إحساس بالخوف", en: "I have to avoid certain things, actions, or places because they cause me fear", category: "PH" },
            { ar: "أشعر بضيق في الأماكن المزدحمة كالأسواق والسينما", en: "I feel discomfort in crowded places like markets and cinemas", category: "PH" },
            { ar: "أشعر بالتوتر عندما أكون بمفردي", en: "I feel tense when I am alone", category: "PH" },
            { ar: "أشعر بالخوف من الاغماء في الأماكن العامة", en: "I feel fear of fainting in public places", category: "PH" },
            { ar: "اعتقد بأن شخصا ما يستطيع السيطرة على أفكاري", en: "I believe someone can control my thoughts", category: "Y" },
            { ar: "أسمع أصوات لا يسمعها الأخرون", en: "I hear voices that others don't hear", category: "Y" },
            { ar: "أعتقد بأن الأخرين يطلعون على أفكاري الخاصة", en: "I believe others can read my private thoughts", category: "Y" },
            { ar: "أشعر بأن أفكاري ليست من صنعي", en: "I feel my thoughts are not my own", category: "Y" },
            { ar: "أشعر بالوحدة حتى عندما أكون مع الأخرين", en: "I feel lonely even when I am with others", category: "Y" },
            { ar: "تراودني أفكار عن الجنس تسبب لي اضطرابا شديدا", en: "I have sexual thoughts that cause me severe disturbance", category: "Y" },
            { ar: "تسيطر عليا أفكار بأنني لابد وأن اعاقب على ذنوبي", en: "I am dominated by thoughts that I must be punished for my sins", category: "Y" },
            { ar: "أعتقد بأن هناك شيئا خطيرا قد حل بجسمي", en: "I believe something serious has happened to my body", category: "Y" },
            { ar: "أشعر بأنني لست قريب من أي شخص أخر", en: "I feel I am not close to any other person", category: "Y" },
            { ar: "أعتقد بأن هناك تغيرا غريبا قد طرأ على أفكاري", en: "I believe a strange change has occurred in my thoughts", category: "Y" },
            { ar: "القي اللوم على الأخرين في معظم متاعبي", en: "I blame others for most of my troubles", category: "P" },
            { ar: "أشعر بعدم الثقة في معظم الناس", en: "I feel distrustful of most people", category: "P" },
            { ar: "أشعر بأن الأخرين يراقبونني أو يتحدثون عني", en: "I feel others are watching me or talking about me", category: "P" },
            { ar: "توجد لدي أفكار أو معتقدات لا أشاركها مع الأخرين", en: "I have thoughts or beliefs I don't share with others", category: "P" },
            { ar: "أشعر بأن الأخرين لا يعطوني ما أستحق من الثناء والتقدير على أعمالي وإنجازاتي", en: "I feel others don't give me the praise and appreciation I deserve for my work and achievements", category: "P" },
            { ar: "أشعر بأن الناس سوف يأخذون فرصتي لو مكنتهم من ذلك", en: "I feel people would take my opportunity if they could", category: "P" }
        ],
        options: [
            { ar: "أبداً", en: "Not at all" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'لا يوجد اضطراب', desc: 'لا توجد مؤشرات واضحة للاضطرابات النفسية. هذا مؤشر جيد على صحتك النفسية.' },
                    { level: 'ميول اضطرابية', desc: 'هناك مؤشرات على وجود ميول اضطرابية تحتاج للمراقبة والمتابعة.' },
                    { level: 'اضطراب معتدل', desc: 'هناك اضطراب نفسي معتدل يؤثر على حياتك. ننصح باستشارة أخصائي نفسي.' },
                    { level: 'اضطراب شديد', desc: 'هناك اضطراب نفسي شديد ويحتاج لتدخل فوري. ننصح بمراجعة أخصائي نفسي بأسرع وقت.' }
                ],
                en: [
                    { level: 'No Disorder', desc: 'There are no clear indicators of psychological disorders. This is a good indicator of your mental health.' },
                    { level: 'Disorder Tendencies', desc: 'There are indicators of disorder tendencies that need monitoring and follow-up.' },
                    { level: 'Moderate Disorder', desc: 'There is a moderate psychological disorder affecting your life. We recommend consulting a psychologist.' },
                    { level: 'Severe Disorder', desc: 'There is a severe psychological disorder that requires immediate intervention. We recommend seeing a psychologist as soon as possible.' }
                ]
            };
            
            if (score <= 180) return results[lang][0];
            if (score <= 360) return results[lang][1];
            if (score <= 540) return results[lang][2];
            return results[lang][3];
        }
    },

    // ========== مقياس الاضطرابات النفسية والعقلية الشائعة ==========
    {
        id: 'common-mental-disorders',
        title: { ar: 'مقياس الاضطرابات النفسية والعقلية الشائعة', en: 'Common Mental Disorders Scale' },
        description: { 
            ar: 'تقييم شامل لسبعة اضطرابات نفسية وعقلية شائعة تشمل الاكتئاب، القلق، الهوس، الفصام، الأعراض الجسدية، اضطرابات الأكل، واضطرابات النوم',
            en: 'Comprehensive assessment of seven common psychological and mental disorders including depression, anxiety, mania, schizophrenia, somatic symptoms, eating disorders, and sleep disorders'
        },
        category: 'women',
        icon: 'fas fa-stethoscope',
        questions: 58,
        time: '12-15',
        rating: 4,
        reviews: 98,
        questions: [
            // الاكتئاب (16 سؤال)
            { ar: "أشعر بالحزن واليأس والضيق", en: "I feel sad, hopeless, and distressed", subscale: "depression" },
            { ar: "فقدت الرغبة بالاستمتاع بالأنشطة من حولي", en: "I lost the desire to enjoy activities around me", subscale: "depression" },
            { ar: "ليس لدي رغبة في الأكل", en: "I have no desire to eat", subscale: "depression" },
            { ar: "نومي مضطرب ومتقطع", en: "My sleep is disturbed and intermittent", subscale: "depression" },
            { ar: "أجد صعوبة في النوم", en: "I have difficulty sleeping", subscale: "depression" },
            { ar: "أشعر بالخمول وقلة النشاط", en: "I feel lethargic and have low activity", subscale: "depression" },
            { ar: "أنزعج هذه الأيام بسهولة", en: "I get annoyed easily these days", subscale: "depression" },
            { ar: "أشعر بالإرهاق المستمر دون بذل أي مجهود", en: "I feel constant exhaustion without any effort", subscale: "depression" },
            { ar: "يراودني شعورا بأنني عديم الفائدة وأنه ليس لي قيمة بالحياة", en: "I feel worthless and that I have no value in life", subscale: "depression" },
            { ar: "أشعر بتأنيب الضمير", en: "I feel guilty", subscale: "depression" },
            { ar: "أجد صعوبة في التركيز على مهمة معينة", en: "I have difficulty concentrating on a specific task", subscale: "depression" },
            { ar: "أعاني من التشتت ولا أستطيع التفكير جيدًا", en: "I suffer from distraction and cannot think well", subscale: "depression" },
            { ar: "أعاني من صعوبة في اتخاذ القرارات", en: "I have difficulty making decisions", subscale: "depression" },
            { ar: "تراودني أفكار بأنه من الأفضل لي أن أموت", en: "I have thoughts that it would be better for me to die", subscale: "depression" },
            { ar: "أعاني من التفكير الزائد (وساوس)", en: "I suffer from overthinking (obsessions)", subscale: "depression" },
            { ar: "تنتابني نوبات بكاء", en: "I experience crying episodes", subscale: "depression" },
            
            // القلق (11 سؤال)
            { ar: "أشعر بالقلق على شيء ما أو شخص ما", en: "I feel anxious about something or someone", subscale: "anxiety" },
            { ar: "أجد صعوبة في السيطرة على مخاوفي وقلقي", en: "I have difficulty controlling my fears and anxiety", subscale: "anxiety" },
            { ar: "ينتابني شعور بأني على حافة الانهيار", en: "I feel like I am on the verge of collapse", subscale: "anxiety" },
            { ar: "أتعب بسرعة", en: "I get tired quickly", subscale: "anxiety" },
            { ar: "أعاني من صعوبة في التركيز", en: "I have difficulty concentrating", subscale: "anxiety" },
            { ar: "أشعر أن عقلي مشتتا", en: "I feel my mind is distracted", subscale: "anxiety" },
            { ar: "أفقد أعصابي بسهولة هذه الأيام", en: "I lose my temper easily these days", subscale: "anxiety" },
            { ar: "أشعر بالتوتر وأنني مشدود داخليا", en: "I feel tense and internally tight", subscale: "anxiety" },
            { ar: "أشعر بزيادة ضربات القلب", en: "I feel increased heartbeats", subscale: "anxiety" },
            { ar: "أشعر ببروده في يداي وقدماي", en: "I feel coldness in my hands and feet", subscale: "anxiety" },
            { ar: "أشعر برعشة في جسمي", en: "I feel tremors in my body", subscale: "anxiety" },
            
            // الهوس (9 أسئلة)
            { ar: "أشعر أنني شخص مهم وعظيم", en: "I feel I am an important and great person", subscale: "mania" },
            { ar: "أشعر أن ثلاث ساعات من النوم كافية لي", en: "I feel three hours of sleep are enough for me", subscale: "mania" },
            { ar: "أحب أن أتكلم باستمرار", en: "I like to talk continuously", subscale: "mania" },
            { ar: "أشعر بأن الأفكار في راسي تتسابق وتتطاير", en: "I feel thoughts in my head are racing and flying", subscale: "mania" },
            { ar: "يتشتت ذهني بسهولة", en: "My mind gets distracted easily", subscale: "mania" },
            { ar: "أشعر بنشاط عالي", en: "I feel high activity", subscale: "mania" },
            { ar: "أسرف في عمليات الشراء", en: "I overspend in shopping", subscale: "mania" },
            { ar: "لدي نشاط جنسي عالي", en: "I have high sexual activity", subscale: "mania" },
            { ar: "أجد نفسي متورط في استثمارات أو أنشطة فاشلة", en: "I find myself involved in failed investments or activities", subscale: "mania" },
            
            // الفصام (13 سؤال)
            { ar: "أسمع أصواتا عندما أكون بمفردي ولا أجد مصدرها", en: "I hear voices when I am alone and cannot find their source", subscale: "schizophrenia" },
            { ar: "أسمع طنين في أذني بين الحين والأخر", en: "I hear ringing in my ears from time to time", subscale: "schizophrenia" },
            { ar: "أشعر بأن شيئا ما يلمس جسمي", en: "I feel something is touching my body", subscale: "schizophrenia" },
            { ar: "أشم روائح لا أحد يشمها غيري", en: "I smell odors that no one else smells", subscale: "schizophrenia" },
            { ar: "أرى أشياء أمامي لا يراها أحد غيري على سبيل المثال : أشخاص أو أشباح أو ظل أو حشرات", en: "I see things in front of me that no one else sees, for example: people, ghosts, shadows, or insects", subscale: "schizophrenia" },
            { ar: "أشعر أن عقلي فقد صوابة", en: "I feel my mind has lost its sanity", subscale: "schizophrenia" },
            { ar: "أعتقد أن هناك مؤامرة تدبر ضدي", en: "I believe there is a conspiracy against me", subscale: "schizophrenia" },
            { ar: "هناك من يحاول السيطرة على أفكاري", en: "Someone is trying to control my thoughts", subscale: "schizophrenia" },
            { ar: "لو لم يضمر لي الناس العداوة لكنت أفضل مما أنا عليه الآن", en: "If people didn't harbor enmity towards me, I would be better than I am now", subscale: "schizophrenia" },
            { ar: "أشعر أن أفكاري ليست من صنعي", en: "I feel my thoughts are not my own", subscale: "schizophrenia" },
            { ar: "أشعر كما لو أنني لا أستطيع السيطرة على سلوكي", en: "I feel as if I cannot control my behavior", subscale: "schizophrenia" },
            { ar: "أصبحت مشاعري متبلده، فكل شيء في نظري سواء", en: "My feelings have become numb, so everything seems the same to me", subscale: "schizophrenia" },
            { ar: "أشعر بأن الآخرين يراقبونني ويتحدثون عني", en: "I feel others are watching me and talking about me", subscale: "schizophrenia" },
            
            // الأعراض الجسدية (11 سؤال)
            { ar: "أصاب بنوبات من الغثيان والقيء", en: "I get episodes of nausea and vomiting", subscale: "somatic" },
            { ar: "تنتابني آلام في المعدة", en: "I experience stomach pains", subscale: "somatic" },
            { ar: "أشكو من آلام في الصدر أو القلب وضيق", en: "I complain of chest or heart pains and tightness", subscale: "somatic" },
            { ar: "لدي إحساس بالقشعريرة والتخدير في أطراف جسمي", en: "I have a sensation of chills and numbness in my limbs", subscale: "somatic" },
            { ar: "أشعر بالإغماء أو بدوخة", en: "I feel faint or dizzy", subscale: "somatic" },
            { ar: "أصبحت مشغولا على صحتي نتيجة الأوجاع", en: "I have become preoccupied with my health due to pains", subscale: "somatic" },
            { ar: "اشكو من آلام في العضلات", en: "I complain of muscle pains", subscale: "somatic" },
            { ar: "أعاني من صداع مستمر", en: "I suffer from constant headaches", subscale: "somatic" },
            { ar: "أشعر بألم أسفل الظهر", en: "I feel pain in my lower back", subscale: "somatic" },
            { ar: "أشعر بثقل في يداي وقدماي", en: "I feel heaviness in my hands and feet", subscale: "somatic" },
            { ar: "أشعر أنني أعاني من مرض خطير وليس له علاج", en: "I feel I am suffering from a serious illness with no cure", subscale: "somatic" },
            
            // اضطرابات الأكل (7 أسئلة)
            { ar: "ليس لدي رغبة في الأكل", en: "I have no desire to eat", subscale: "eating" },
            { ar: "ألاحظ وجود تغيير في وزني", en: "I notice a change in my weight", subscale: "eating" },
            { ar: "أعاني من نقص واضح في التغذية", en: "I suffer from obvious nutritional deficiency", subscale: "eating" },
            { ar: "اعتمد على الفيتامينات والمكملات الغذائية للحفاظ على صحتي", en: "I rely on vitamins and nutritional supplements to maintain my health", subscale: "eating" },
            { ar: "أكتفي بوجبة واحدة طوال اليوم", en: "I suffice with one meal throughout the day", subscale: "eating" },
            { ar: "أشعر أن الأكل غير مناسب لي", en: "I feel food is not suitable for me", subscale: "eating" },
            { ar: "أشعر أن الأكل يضر في صحتي", en: "I feel food harms my health", subscale: "eating" },
            
            // اضطرابات النوم (8 أسئلة)
            { ar: "نومي مضطرب ومتقطع", en: "My sleep is disturbed and intermittent", subscale: "sleep" },
            { ar: "أجد صعوبة في النوم", en: "I have difficulty sleeping", subscale: "sleep" },
            { ar: "أشعر أن ثلاث ساعات من النوم كافية لي", en: "I feel three hours of sleep are enough for me", subscale: "sleep" },
            { ar: "أجد صعوبة في البدء بالنوم", en: "I have difficulty starting sleep", subscale: "sleep" },
            { ar: "أجد صعوبة في الحفاظ على النوم", en: "I have difficulty maintaining sleep", subscale: "sleep" },
            { ar: "أستيقظ فزعا في الصباح الباكر", en: "I wake up frightened early in the morning", subscale: "sleep" },
            { ar: "نومي غير مستقر", en: "My sleep is unstable", subscale: "sleep" },
            { ar: "أعاني من الكوابيس", en: "I suffer from nightmares", subscale: "sleep" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'لا يوجد اضطراب', desc: 'لا توجد مؤشرات واضحة للاضطرابات النفسية. هذا مؤشر جيد على صحتك النفسية.' },
                    { level: 'اضطراب بسيط', desc: 'هناك مؤشرات على وجود اضطراب نفسي بسيط. قد تحتاج لمتابعة حالتك.' },
                    { level: 'اضطراب متوسط', desc: 'هناك اضطراب نفسي متوسط يؤثر على حياتك. ننصح باستشارة أخصائي.' },
                    { level: 'اضطراب شديد', desc: 'هناك اضطراب نفسي شديد ويحتاج لتدخل فوري. ننصح بمراجعة أخصائي نفسي.' }
                ],
                en: [
                    { level: 'No Disorder', desc: 'There are no clear indicators of psychological disorders. This is a good indicator of your mental health.' },
                    { level: 'Mild Disorder', desc: 'There are indicators of mild psychological disorder. You may need to monitor your condition.' },
                    { level: 'Moderate Disorder', desc: 'There is a moderate psychological disorder affecting your life. We recommend consulting a specialist.' },
                    { level: 'Severe Disorder', desc: 'There is a severe psychological disorder that requires immediate intervention. We recommend seeing a psychologist.' }
                ]
            };
            
            if (score <= 116) return results[lang][0];
            if (score <= 232) return results[lang][1];
            if (score <= 348) return results[lang][2];
            return results[lang][3];
        }
    },

    // ========== مقياس الذكاءات المتعددة (MIS - Shearer) ==========
    {
        id: 'multiple-intelligences',
        title: { 
            ar: 'مقياس الذكاءات المتعددة', 
            en: 'Multiple Intelligences Scale' 
        },
        description: { 
            ar: 'مقياس لتقييم 8 أنواع من الذكاءات المختلفة وفق نظرية الذكاءات المتعددة. يتكون من 93 سؤالاً يقيس قدراتك في مختلف المجالات.',
            en: 'A scale to assess 8 different types of intelligences according to Multiple Intelligences Theory. Consists of 93 questions measuring your abilities in various domains.'
        },
        category: 'personality',
        icon: 'fas fa-brain',
        time: '20-25 دقيقة',
        questions: 93,
        rating: 4.5,
        reviews: 89,
        questions: [
            // ========== الذكاء الموسيقي - الإيقاعي (11 سؤال) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن تعلمت العزف على آلة موسيقية أو تلقيت دروساً موسيقية؟",
                    en: "Have you ever learned to play a musical instrument or received music lessons?"
                },
                required: true,
                options: [
                    { ar: "مرة أو مرتين", en: "Once or twice", value: 1 },
                    { ar: "ثلاث أو أربع مرات تقريباً", en: "About three or four times", value: 2 },
                    { ar: "لمدة شهرين", en: "For two months", value: 3 },
                    { ar: "أقل من سنة", en: "Less than a year", value: 4 },
                    { ar: "أكثر من سنة", en: "More than a year", value: 5 },
                    { ar: "لم أحصل على فرصة لتعلم ذلك", en: "I haven't had the opportunity to learn that", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تستطيع الغناء؟",
                    en: "To what extent can you sing?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "جيداً نوعاً ما", en: "Somewhat good", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تعتقد أن بإمكانك أن تصبح موسيقياً أو مطرباً جيداً لو حاولت؟",
                    en: "Do you think you could become a good musician or singer if you tried?"
                },
                required: true,
                options: [
                    { ar: "لا أعتقد ذلك", en: "I don't think so", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "أعتقد سأكون جيداً نوعاً ما", en: "I think I would be somewhat good", value: 3 },
                    { ar: "أعتقد سأكون موسيقياً جيداً", en: "I think I would be a good musician", value: 4 },
                    { ar: "أعتقد سأكون موسيقياً عظيماً", en: "I think I would be a great musician", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تشعر أن في ذهنك عادة لحناً أو أغنية معينة أو تدندن وتغني لنفسك؟",
                    en: "Do you usually have a tune or specific song in your mind, or do you hum and sing to yourself?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 3 },
                    { ar: "في جميع الأوقات تقريباً", en: "Almost all the time", value: 4 },
                    { ar: "دائماً", en: "Always", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن كتبت أغنية أو أنشودة بهدف اللهو والاستمتاع؟",
                    en: "Have you ever written a song or chant for fun and enjoyment?"
                },
                required: true,
                options: [
                    { ar: "لا مطلقاً", en: "Never", value: 1 },
                    { ar: "ربما مرة واحدة أو مرتين", en: "Maybe once or twice", value: 2 },
                    { ar: "أحياناً أفعل", en: "I sometimes do", value: 3 },
                    { ar: "مرات كثيرة", en: "Many times", value: 4 },
                    { ar: "دائماً", en: "Always", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يكون تصفيق يديك أو ضرب رجليك مطابقاً للحن أو إيقاع معين؟",
                    en: "To what extent does your hand clapping or foot tapping match a specific melody or rhythm?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تستمع لبعض الأغاني أو لموسيقى معينة بشكل متكرر؟",
                    en: "Do you listen to certain songs or music repeatedly?"
                },
                required: true,
                options: [
                    { ar: "ربما مرة واحدة أو مرتين", en: "Maybe once or twice", value: 1 },
                    { ar: "أحياناً أفعل", en: "I sometimes do", value: 2 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب حصص الموسيقى ودروسها، أو الأداء الموسيقي؟",
                    en: "Do you like music classes and lessons, or musical performance?"
                },
                required: true,
                options: [
                    { ar: "لا أبداً", en: "Never", value: 1 },
                    { ar: "قليلاً جداً", en: "Very little", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "لا بأس", en: "Not bad", value: 4 },
                    { ar: "كثيراً", en: "A lot", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تتوجه نحو صوت الموسيقى وتصفق بيديك وترقص معها؟",
                    en: "Do you gravitate towards music, clap your hands and dance with it?"
                },
                required: true,
                options: [
                    { ar: "أبداً أو نادراً", en: "Never or rarely", value: 1 },
                    { ar: "مرة بين الحين والآخر", en: "Once in a while", value: 2 },
                    { ar: "أحياناً أفعل ذلك", en: "I sometimes do that", value: 3 },
                    { ar: "عادة", en: "Usually", value: 4 },
                    { ar: "دائماً", en: "Always", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل عادة تغني مع المغني أثناء سماعك للموسيقى من الأشرطة أو التلفاز؟",
                    en: "Do you usually sing along with the singer while listening to music from tapes or TV?"
                },
                required: true,
                options: [
                    { ar: "بين الحين والآخر", en: "From time to time", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "musical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تستطيع العزف على آلة موسيقية؟",
                    en: "To what extent can you play a musical instrument?"
                },
                required: true,
                options: [
                    { ar: "قليلاً جداً", en: "Very little", value: 1 },
                    { ar: "إلى حد ما جيد", en: "Somewhat good", value: 2 },
                    { ar: "بدرجة جيدة", en: "To a good degree", value: 3 },
                    { ar: "بدرجة جيدة جداً", en: "To a very good degree", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف أو لم أحاول أبداً", en: "I don't know or never tried", value: 0 }
                ],
                category: "musical"
            },

            // ========== الذكاء الجسمي - الحركي (10 أسئلة) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك الركض، أو القفز، أو العدو (كركض الفرس)؟",
                    en: "To what extent can you run, jump, or gallop (like a horse running)?"
                },
                required: true,
                options: [
                    { ar: "جيد نوعاً ما", en: "Somewhat good", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تستطيع التدحرج أو التشقلب (الشقلبة)؟",
                    en: "To what extent can you roll or somersault?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تشعر عادة بأنك ترغب بأن تقوم بأعمال معينة، مثل: ركوب الدراجة الهوائية أو القفز فوق الحبل، وغيرها؟",
                    en: "Do you usually feel like doing certain activities, such as riding a bicycle or jumping rope, etc.?"
                },
                required: true,
                options: [
                    { ar: "بين الحين والآخر", en: "From time to time", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك استخدام يديك في ألعاب معينة مثل: خلط الورق البطة، وألعاب الخدع؟",
                    en: "To what extent can you use your hands in certain games like card shuffling, trick games?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "لا بأس", en: "Not bad", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك استخدام الإبرة للخياطة، أو استخدام المقص لتقطيع الورق، أو تركيب أجزاء اللعبة معاً كألعاب الفك والتركيب، ووضع الأشياء الصغيرة معاً؟",
                    en: "To what extent can you use a needle for sewing, or use scissors to cut paper, or assemble toy parts like puzzle games, and put small things together?"
                },
                required: true,
                options: [
                    { ar: "لا بأس", en: "Not bad", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن استخدمت جسمك أو وجهك لتقليد شخص معين، مثل: الأصدقاء أو المعلمين أو تقليد شخص ما على التلفاز؟",
                    en: "Have you ever used your body or face to imitate a specific person, such as friends, teachers, or imitating someone on TV?"
                },
                required: true,
                options: [
                    { ar: "لا أو ربما مرة واحدة", en: "No or maybe once", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك الرقص أو الحركة بمصاحبة الأنغام (الإيقاعات) الموسيقية؟",
                    en: "To what extent can you dance or move to musical rhythms?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "لا بأس", en: "Not bad", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك التوازن عند الوقوف على رجل واحدة، أو المشي على عارضة التوازن، أو على حافة الرصيف؟",
                    en: "To what extent can you balance when standing on one leg, or walking on a balance beam, or on the edge of the sidewalk?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً، أو لا بأس", en: "Not good, or not bad", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة أنت جيد في الألعاب الرياضية والجسدية، مثل: لعبة القفز فوق الحبل، أو الكاراتيه، أو القيام بحركات بهلوانية؟",
                    en: "To what extent are you good at sports and physical games, such as jump rope, karate, or performing acrobatic movements?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن اخترعت حركات جديدة لرقصة أو لعبة معينة مثل: لعبة كرة القدم؟",
                    en: "Have you ever invented new movements for a dance or a specific game like football?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "ربما مرة أو مرتين", en: "Maybe once or twice", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "bodily"
            },

            // ========== الذكاء المنطقي - الرياضي (9 أسئلة) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "عندما كنت صغيراً، إلى أي درجة كان سهلاً عليك تعلم الأرقام والأعداد؟",
                    en: "When you were young, to what extent was it easy for you to learn numbers and numerals?"
                },
                required: true,
                options: [
                    { ar: "كان صعباً", en: "It was difficult", value: 1 },
                    { ar: "كان سهلاً إلى حد ما", en: "It was somewhat easy", value: 2 },
                    { ar: "كان سهلاً", en: "It was easy", value: 3 },
                    { ar: "كان سهلاً جداً", en: "It was very easy", value: 4 },
                    { ar: "تعلمت بسرعة أكثر من معظم الأطفال", en: "I learned faster than most children", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل كان صعباً عليك أن تتعلم عملية الطرح؟",
                    en: "Was it difficult for you to learn subtraction?"
                },
                required: true,
                options: [
                    { ar: "كان صعباً", en: "It was difficult", value: 1 },
                    { ar: "كان سهلاً إلى حد ما", en: "It was somewhat easy", value: 2 },
                    { ar: "كان سهلاً", en: "It was easy", value: 3 },
                    { ar: "كان سهلاً جداً", en: "It was very easy", value: 4 },
                    { ar: "تعلمت بسرعة أكثر من معظم الأطفال", en: "I learned faster than most children", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة أنت جيد في الرياضيات؟",
                    en: "To what extent are you good at mathematics?"
                },
                required: true,
                options: [
                    { ar: "لست جيداً", en: "I'm not good", value: 1 },
                    { ar: "حول المتوسط", en: "Around average", value: 2 },
                    { ar: "أفضل من المتوسط", en: "Better than average", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب العلوم وحل المشكلات، وقياس الأشياء، وإجراء التجارب؟",
                    en: "Do you like science, problem solving, measuring things, and conducting experiments?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "بدرجة متوسطة", en: "To a medium degree", value: 3 },
                    { ar: "أكثر من معظم الأشخاص", en: "More than most people", value: 4 },
                    { ar: "أكثر من أي شخص آخر أعرفه", en: "More than anyone else I know", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل كان يصعب عليك أن تتعلم عملية القسمة؟",
                    en: "Was it difficult for you to learn division?"
                },
                required: true,
                options: [
                    { ar: "نعم، كان صعباً", en: "Yes, it was difficult", value: 1 },
                    { ar: "كان صعباً إلى حد ما", en: "It was somewhat difficult", value: 2 },
                    { ar: "كان سهلاً نوعاً ما", en: "It was somewhat easy", value: 3 },
                    { ar: "كان سهلاً", en: "It was easy", value: 4 },
                    { ar: "تعلمت بسرعة أكبر من معظم الأشخاص", en: "I learned faster than most people", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحاول عادة أن تعرف كيف تسير الأشياء، ولماذا؟",
                    en: "Do you usually try to understand how things work, and why?"
                },
                required: true,
                options: [
                    { ar: "نادراً، بين الحين والآخر", en: "Rarely, from time to time", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف، أو لم تسنح لي الفرصة", en: "I don't know, or haven't had the opportunity", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق أن جمعت أشياء وحاولت معرفة كل شيء عنها، مثل: الطوابع، صور الحيوانات، وصور أخرى كصور لاعبي كرة القدم، والدمى، والحواسيب.. الخ؟",
                    en: "Have you ever collected things and tried to learn everything about them, such as stamps, animal pictures, and other pictures like football players, dolls, computers, etc.?"
                },
                required: true,
                options: [
                    { ar: "لا أو قليلاً", en: "No or a little", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف، أو لم تسنح لي الفرصة", en: "I don't know, or haven't had the opportunity", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يكون سهلاً عليك أن تصنف الأشياء وتنظمها؟",
                    en: "To what extent is it easy for you to classify and organize things?"
                },
                required: true,
                options: [
                    { ar: "لم يسبق لك أن نظمت الأشياء", en: "You have never organized things", value: 1 },
                    { ar: "صعب جداً", en: "Very difficult", value: 2 },
                    { ar: "أحياناً يكون سهلاً", en: "Sometimes easy", value: 3 },
                    { ar: "في أحيان كثيرة يكون سهلاً", en: "Often easy", value: 4 },
                    { ar: "دائماً يكون سهلاً جداً", en: "Always very easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "logical"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تستطيع التعامل مع الكسور العادية، والكسور العشرية، والنسب؟",
                    en: "To what extent can you handle fractions, decimals, and percentages?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "لا بأس", en: "Not bad", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف أو لم أفعل ذلك", en: "I don't know or haven't done that", value: 0 }
                ],
                category: "logical"
            },

            // ========== الذكاء المكاني - البصري (10 أسئلة) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب تزيين غرفتك بالصور واللوحات والرسومات؟",
                    en: "Do you like decorating your room with pictures, paintings, drawings?"
                },
                required: true,
                options: [
                    { ar: "ليس كثيراً", en: "Not much", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف أو لم تسنح لي الفرصة بذلك", en: "I don't know or haven't had the opportunity", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن قمت بالرسم أو تزيين دفاترك أو الهدايا؟",
                    en: "Have you ever drawn or decorated your notebooks or gifts?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تقوم بأعمال فنية أو حرفية مثل: القص واللصق، وعمل الطائرات الورقية، والرسم، وتلوين الكتب؟",
                    en: "Do you do artistic or craft work such as cutting and pasting, making paper airplanes, drawing, coloring books?"
                },
                required: true,
                options: [
                    { ar: "عادة", en: "Usually", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "كم أنت جيد في فك الأشياء عن بعضها البعض ثم إعادتها معاً مرة أخرى مثل: لعبة الصور المجزأة والدمى؟",
                    en: "How good are you at taking things apart and then putting them back together again, like puzzle games and dolls?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "لا بأس", en: "Not bad", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل لديك ميول لتصميم بطاقات أو ملابس أو أعمال يدوية أو اختراع شيء معين؟",
                    en: "Do you have inclinations towards designing cards, clothes, handmade works, or inventing something specific?"
                },
                required: true,
                options: [
                    { ar: "عادة", en: "Usually", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تنشغل عادة بالرسم أو تلوين الصور؟",
                    en: "Do you usually engage in drawing or coloring pictures?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات دائماً", en: "Always all the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب حصص الفن؟",
                    en: "Do you like art classes?"
                },
                required: true,
                options: [
                    { ar: "قليلاً", en: "A little", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في كثير من الأحيان", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات دائماً", en: "Always all the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل أنت ماهر في ممارسة الألعاب الرياضية مثل: لعبة الريشة الطائرة، وكرة السلة؟",
                    en: "Are you skilled in practicing sports games like badminton, basketball?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "جيداً إلى حد ما", en: "Somewhat good", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب عادة استخدام الكاميرا، والنظر في الصور الشخصية (الفوتوغرافية) أو الصور التي في الكتب؟",
                    en: "Do you usually like using the camera, looking at personal (photographic) pictures or pictures in books?"
                },
                required: true,
                options: [
                    { ar: "أبداً أو نادراً", en: "Never or rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل أنت جيد في الأنشطة كالأعمال الحرفية اليدوية سواء في المدرسة أو في البيت أو المشاركة في المخيمات؟",
                    en: "Are you good at activities like manual crafts whether at school or at home or participating in camps?"
                },
                required: true,
                options: [
                    { ar: "لا بأس", en: "Not bad", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "spatial"
            },

            // ========== الذكاء اللغوي - اللفظي (13 سؤال) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة كان صعباً عليك تعلم الأحرف الأبجدية أو تعلم القراءة؟",
                    en: "To what extent was it difficult for you to learn the alphabet or learn to read?"
                },
                required: true,
                options: [
                    { ar: "كان صعباً", en: "It was difficult", value: 1 },
                    { ar: "كان سهلاً نوعاً ما", en: "It was somewhat easy", value: 2 },
                    { ar: "كان سهلاً", en: "It was easy", value: 3 },
                    { ar: "كان سهلاً جداً", en: "It was very easy", value: 4 },
                    { ar: "تعلمت بسرعة أكبر من معظم الأشخاص", en: "I learned faster than most people", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك أن تحفظ القصائد والأناشيد والأغاني عن ظهر قلب؟",
                    en: "To what extent can you memorize poems, anthems, and songs by heart?"
                },
                required: true,
                options: [
                    { ar: "لا بأس", en: "Not bad", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تتحدث كثيراً عن الكتب أو الأفلام أو المسلسلات أو برامج التلفزيون التي تحبها؟",
                    en: "Do you talk a lot about books, movies, series, or TV programs that you like?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك القراءة بشكل جيد؟",
                    en: "To what extent can you read well?"
                },
                required: true,
                options: [
                    { ar: "لا بأس", en: "Not bad", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك رواية القصص أو قمت بإلقاء الشعر أو كتابة كلمات لأغاني؟",
                    en: "Have you ever told stories, recited poetry, or written song lyrics?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تتقن مهارة التحدث مع الناس عن كيفية عملك لشيء معين؟",
                    en: "To what extent do you master the skill of talking to people about how you do something?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً على الإطلاق", en: "Not good at all", value: 1 },
                    { ar: "جيد إلى حد ما", en: "Somewhat good", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تفهم التوجيهات التي تتلقاها من الآخرين؟",
                    en: "To what extent do you understand the directions you receive from others?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "لا بأس", en: "Not bad", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن استخدمت القاموس لإيجاد معنى لكلمة ما أو لمعرفة الاستخدام الصحيح للكلمة؟",
                    en: "Have you ever used a dictionary to find the meaning of a word or to know the correct usage of a word?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق أن حاولت استخدام كلمات كبيرة سمعتها ممن هم أكبر منك سناً؟",
                    en: "Have you ever tried to use big words you heard from people older than you?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تستمتع بالعمل في الأعمال المكتبية؟",
                    en: "Do you enjoy working in office work?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "كثيراً", en: "A lot", value: 4 },
                    { ar: "كثيراً جداً", en: "Very much", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك كتابة ملاحظة أو رسالة إلى شخص ما؟",
                    en: "To what extent can you write a note or letter to someone?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "جيد إلى حد ما", en: "Somewhat good", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من السهل عليك إيجاد الكلمات المناسبة للتعبير عما تشعر به أو تفكر به؟",
                    en: "Is it easy for you to find the right words to express what you feel or think?"
                },
                required: true,
                options: [
                    { ar: "لا، إنه أمر صعب علي", en: "No, it's difficult for me", value: 1 },
                    { ar: "إنه أمر سهل إلى حد ما", en: "It's somewhat easy", value: 2 },
                    { ar: "إنه سهل", en: "It's easy", value: 3 },
                    { ar: "بالعادة سهل", en: "Usually easy", value: 4 },
                    { ar: "يسهل علي في معظم الأوقات", en: "Easy for me most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق أن كتبت كتاباً أو قصة أو قصيدة على سبيل التسلية؟",
                    en: "Have you ever written a book, story, or poem for entertainment?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "ربما مرة واحدة", en: "Maybe once", value: 2 },
                    { ar: "عدة مرات", en: "Several times", value: 3 },
                    { ar: "مرات كثيرة", en: "Many times", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "linguistic"
            },

            // ========== الذكاء الشخصي - الذاتي (13 سؤال) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن عرضت المساعدة على جيرانك أو على زملائك؟",
                    en: "Have you ever offered help to your neighbors or colleagues?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يمكنك مساعدة الآخرين في إنهاء الجدل أو النقاش بينهم، كالجدل الذي يحدث بين صديقين؟",
                    en: "To what extent can you help others end arguments or discussions between them, such as arguments between two friends?"
                },
                required: true,
                options: [
                    { ar: "ليس جيداً", en: "Not good", value: 1 },
                    { ar: "جيد نوعاً ما", en: "Somewhat good", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق أن كنت قائداً في المدرسة أو بين الأصدقاء؟",
                    en: "Have you ever been a leader at school or among friends?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 4 },
                    { ar: "دائماً على الأغلب", en: "Always most likely", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تلعب مع مجموعة معينة من الأشخاص أو صديقك المفضل؟",
                    en: "Do you play with a specific group of people or your best friend?"
                },
                required: true,
                options: [
                    { ar: "أحياناً", en: "Sometimes", value: 1 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 2 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 3 },
                    { ar: "في جميع الأوقات تقريباً", en: "Almost all the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تجد صعوبة في معرفة مشاعر الآخرين، مثل: مشاعر الحزن، السعادة، الغضب، الدهشة؟",
                    en: "Do you find it difficult to know others' feelings, such as feelings of sadness, happiness, anger, surprise?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من الصعب عليك أن تحل مشكلة مع صديقك أو أخيك أو أختك بدون نقاش أو جدال معهم؟",
                    en: "Is it difficult for you to solve a problem with your friend, brother, or sister without discussion or argument with them?"
                },
                required: true,
                options: [
                    { ar: "في أحيان كثيرة يكون صعب علي", en: "Often difficult for me", value: 1 },
                    { ar: "أحياناً يكون صعباً علي", en: "Sometimes difficult for me", value: 2 },
                    { ar: "يصادف مرة أن كان صعباً علي", en: "Occasionally difficult for me", value: 3 },
                    { ar: "مرات عديدة يكون سهلاً علي", en: "Often easy for me", value: 4 },
                    { ar: "دائماً ما يكون سهلاً علي", en: "Always easy for me", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة يكون سهلاً عليك أن تتخذ لنفسك صديقاً جديداً؟",
                    en: "To what extent is it easy for you to make a new friend for yourself?"
                },
                required: true,
                options: [
                    { ar: "يمكن أن يكون صعباً", en: "It can be difficult", value: 1 },
                    { ar: "أحياناً يكون سهلاً", en: "Sometimes easy", value: 2 },
                    { ar: "في أحيان كثيرة يكون سهلاً", en: "Often easy", value: 3 },
                    { ar: "في معظم الأوقات يكون سهلاً", en: "Most of the time easy", value: 4 },
                    { ar: "في جميع الأوقات يكون سهلاً", en: "All the time easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من السهل عليك أن تجعل الآخرين يفعلون الأشياء بطريقتك الخاصة؟",
                    en: "Is it easy for you to make others do things your way?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "مرات عديدة", en: "Numerous times", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب أن تكون عضواً في مجموعة (أو فريق ما)، وأن يكون لديك دور أو نشاط في هذه المجموعة؟",
                    en: "Do you like to be a member of a group (or team), and to have a role or activity in this group?"
                },
                required: true,
                options: [
                    { ar: "أحياناً", en: "Sometimes", value: 1 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 2 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 3 },
                    { ar: "في جميع الأوقات تقريباً", en: "Almost all the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن ساعدت صديقاً لك ليتعلم شيئاً جديداً أو يحل مشكلة ما؟",
                    en: "Have you ever helped a friend learn something new or solve a problem?"
                },
                required: true,
                options: [
                    { ar: "ربما مرة واحدة أو مرتين", en: "Maybe once or twice", value: 1 },
                    { ar: "عدة مرات", en: "Several times", value: 2 },
                    { ar: "مرات كثيرة", en: "Many times", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "كم هو صعب عليك أن تفهم ما يتوقع والدك أو معلمك منك؟",
                    en: "How difficult is it for you to understand what your parent or teacher expects from you?"
                },
                required: true,
                options: [
                    { ar: "دائماً صعب", en: "Always difficult", value: 1 },
                    { ar: "مرات عديدة يكون صعباً", en: "Often difficult", value: 2 },
                    { ar: "أحياناً صعب", en: "Sometimes difficult", value: 3 },
                    { ar: "في أحيان كثيرة سهل", en: "Often easy", value: 4 },
                    { ar: "دائماً سهل", en: "Always easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن عرفت ما هو الشيء الصحيح لتفعله أو لتقوله في الأمور أو المواقف التي تتطلب سرعة الاستجابة والبديهة؟",
                    en: "Have you ever known what is the right thing to do or say in matters or situations that require quick response and intuition?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 4 },
                    { ar: "على الأغلب دائماً", en: "Most likely always", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من السهل عليك أن تدرك أن شخصاً ما في مزاج سيئ؟",
                    en: "Is it easy for you to realize that someone is in a bad mood?"
                },
                required: true,
                options: [
                    { ar: "أحياناً", en: "Sometimes", value: 1 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 2 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 3 },
                    { ar: "في جميع الأوقات تقريباً", en: "Almost all the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "intrapersonal"
            },

            // ========== الذكاء الاجتماعي - التفاعلي (14 سؤال) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تختار نشاطات تتطلب منك التحدي لتقوم بها؟",
                    en: "Do you choose activities that require challenge for you to do?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل يصعب عليك ضبط مشاعرك ومزاجك؟",
                    en: "Is it difficult for you to control your feelings and mood?"
                },
                required: true,
                options: [
                    { ar: "نعم، صعب جداً", en: "Yes, very difficult", value: 1 },
                    { ar: "مرات عديدة يكون صعب", en: "Often difficult", value: 2 },
                    { ar: "أحياناً يكون صعب", en: "Sometimes difficult", value: 3 },
                    { ar: "مرات عديدة يكون سهلاً", en: "Often easy", value: 4 },
                    { ar: "دائماً يكون سهل", en: "Always easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل يسهل عليك الانتقال من نشاط لآخر عندما يطلب إليك ذلك؟",
                    en: "Is it easy for you to transition from one activity to another when requested?"
                },
                required: true,
                options: [
                    { ar: "صعب جداً", en: "Very difficult", value: 1 },
                    { ar: "مرات عديدة يكون صعب", en: "Often difficult", value: 2 },
                    { ar: "أحياناً يكون صعب", en: "Sometimes difficult", value: 3 },
                    { ar: "مرات عديدة يكون سهلاً", en: "Often easy", value: 4 },
                    { ar: "دائماً يكون سهل", en: "Always easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل يمكنك اكتشاف أخطائك؟",
                    en: "Can you detect your mistakes?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من السهل عليك التركيز؟",
                    en: "Is it easy for you to concentrate?"
                },
                required: true,
                options: [
                    { ar: "صعب جداً", en: "Very difficult", value: 1 },
                    { ar: "مرات عديدة يكون صعب", en: "Often difficult", value: 2 },
                    { ar: "سهل", en: "Easy", value: 3 },
                    { ar: "سهل جداً", en: "Very easy", value: 4 },
                    { ar: "دائماً يكون سهل جداً", en: "Always very easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تعمل لوحدك بشكل جيد؟",
                    en: "Do you work alone well?"
                },
                required: true,
                options: [
                    { ar: "ليست جيداً", en: "Not well", value: 1 },
                    { ar: "جيد إلى حد ما", en: "Somewhat good", value: 2 },
                    { ar: "جيد", en: "Good", value: 3 },
                    { ar: "جيد جداً", en: "Very good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "عندما تقوم بعمل ما، هل يكون لديك فكرة واضحة كيف سيكون عندما يتم إنجازه؟",
                    en: "When you do a task, do you have a clear idea of how it will be when completed?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "على الأغلب دائماً", en: "Most likely always", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تفكر عادة فيما تريد أن تكون عليه عندما تكبر؟",
                    en: "Do you usually think about what you want to be when you grow up?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تحب أن تعتمد على نفسك في عمل الأشياء واتخاذ القرارات؟",
                    en: "Do you like to rely on yourself in doing things and making decisions?"
                },
                required: true,
                options: [
                    { ar: "أحياناً", en: "Sometimes", value: 1 },
                    { ar: "نعم في أحيان كثيرة", en: "Yes often", value: 2 },
                    { ar: "معظم الوقت نعم", en: "Most of the time yes", value: 3 },
                    { ar: "في جميع الأوقات تقريباً", en: "Almost all the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل يمكنك أن تجد طرقاً للترويح عن نفسك عندما تشعر بالحزن أو الإحباط؟",
                    en: "Can you find ways to entertain yourself when you feel sad or frustrated?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 3 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تتخذ قرارات أو تضع خططاً جيدة خاصة بك؟",
                    en: "Do you make good decisions or plans of your own?"
                },
                required: true,
                options: [
                    { ar: "أحياناً", en: "Sometimes", value: 1 },
                    { ar: "في أحيان كثيرة", en: "Often", value: 2 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 3 },
                    { ar: "دائماً على الأغلب", en: "Always most likely", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تستطيع أن تبقى نفسك مشغولاً، سعيداً، أو مسلياً عندما تكون لوحدك؟",
                    en: "Can you keep yourself busy, happy, or entertained when you are alone?"
                },
                required: true,
                options: [
                    { ar: "نادراً", en: "Rarely", value: 1 },
                    { ar: "أحياناً", en: "Sometimes", value: 2 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 3 },
                    { ar: "في جميع الأوقات تقريباً", en: "Almost all the time", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل يسهل عليك أن تنظم نفسك وتعد واجباتك وأعمالك الأخرى؟",
                    en: "Is it easy for you to organize yourself and prepare your homework and other tasks?"
                },
                required: true,
                options: [
                    { ar: "إنه صعب", en: "It's difficult", value: 1 },
                    { ar: "إلى حد ما يسهل علي", en: "Somewhat easy for me", value: 2 },
                    { ar: "مرات عديدة يكون سهلاً", en: "Often easy", value: 3 },
                    { ar: "غالباً يكون سهلاً", en: "Usually easy", value: 4 },
                    { ar: "دائماً يكون سهل جداً", en: "Always very easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "إلى أي درجة تعرف عن مهاراتك وقدراتك؟",
                    en: "To what extent do you know about your skills and abilities?"
                },
                required: true,
                options: [
                    { ar: "جيد إلى حد ما", en: "Somewhat good", value: 1 },
                    { ar: "جيد", en: "Good", value: 2 },
                    { ar: "جيد جداً", en: "Very good", value: 3 },
                    { ar: "ممتاز", en: "Excellent", value: 4 },
                    { ar: "ممتاز جداً", en: "Very excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "interpersonal"
            },

            // ========== الذكاء الطبيعي - البيئي (13 سؤال) ==========
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن اعتنيت بتربية حيوان أليف؟",
                    en: "Have you ever taken care of a pet?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "قليلاً، مرة واحدة", en: "A little, once", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "بشكل متكرر", en: "Frequently", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من السهل عليك أن تتعلم كيف تعتني بحيوان ما؟",
                    en: "Is it easy for you to learn how to take care of an animal?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "إلى حد ما", en: "Somewhat", value: 3 },
                    { ar: "سهل", en: "Easy", value: 4 },
                    { ar: "سهل جداً", en: "Very easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل سبق لك أن كنت جيداً في ترويض حيوان أو تدريبه على حركات معينة؟",
                    en: "Have you ever been good at taming an animal or training it on specific movements?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "جيد إلى حد ما", en: "Somewhat good", value: 3 },
                    { ar: "جيد", en: "Good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل فكرت يوماً أن تكون طبيباً بيطرياً لتعالج الحيوانات؟",
                    en: "Have you ever thought about becoming a veterinarian to treat animals?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "بقدر كافي", en: "Enough", value: 4 },
                    { ar: "في جميع الأوقات", en: "All the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل يمكنك بسهولة تمييز الحيوانات أو إيجاد الاختلاف بين أنواعها وفصائلها؟",
                    en: "Can you easily distinguish animals or find differences between their types and breeds?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "إلى حد ما", en: "Somewhat", value: 3 },
                    { ar: "إنه سهل", en: "It's easy", value: 4 },
                    { ar: "سهل جداً", en: "Very easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل لديك حب استطلاع للطبيعة، والبحث عن الحيوانات وجمع النباتات والحشرات، وغيرها من الأشياء؟",
                    en: "Do you have curiosity about nature, searching for animals and collecting plants, insects, and other things?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "نادراً", en: "Rarely", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "بشكل متكرر", en: "Frequently", value: 4 },
                    { ar: "في معظم الأوقات", en: "Most of the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تهتم بالبيئة وتحاول التفكير في طرق ووسائل لإيقاف التلوث أو مساعدة الحيوان؟",
                    en: "Do you care about the environment and try to think of ways and means to stop pollution or help animals?"
                },
                required: true,
                options: [
                    { ar: "قليلاً جداً", en: "Very little", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "نعم كثيراً", en: "Yes a lot", value: 4 },
                    { ar: "نعم، في جميع الأوقات", en: "Yes, all the time", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل أنت جيد في زراعة النباتات والعناية بالحدائق؟",
                    en: "Are you good at planting plants and taking care of gardens?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "إلى حد ما", en: "Somewhat", value: 3 },
                    { ar: "نعم جيد", en: "Yes good", value: 4 },
                    { ar: "ممتاز", en: "Excellent", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تتعلم بسهولة عن أنواع النباتات المختلفة؟",
                    en: "Do you easily learn about different types of plants?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "إلى حد ما", en: "Somewhat", value: 3 },
                    { ar: "سهل", en: "Easy", value: 4 },
                    { ar: "سهل جداً", en: "Very easy", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل لديك اهتمام زائد أو مهارة في العلوم والطبيعة أو التعلم عن الكهرباء أو الكيمياء؟",
                    en: "Do you have excessive interest or skill in science and nature or learning about electricity or chemistry?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "إلى حد ما", en: "Somewhat", value: 3 },
                    { ar: "بقدر كافي", en: "Enough", value: 4 },
                    { ar: "كثيراً جداً", en: "Very much", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل تتقن واحداً أو أكثر من الآتي: الطهو، التسوق، اكتشاف الطبيعة، صيد الأسماك، المشاركة في المخيمات؟",
                    en: "Are you proficient in one or more of the following: cooking, shopping, exploring nature, fishing, participating in camps?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "أحياناً", en: "Sometimes", value: 3 },
                    { ar: "بقدر كافي", en: "Enough", value: 4 },
                    { ar: "كثيراً جداً", en: "Very much", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل ترسم صوراً أو تروي قصصاً أو تكتب عن الطبيعة أو الحيوانات؟",
                    en: "Do you draw pictures, tell stories, or write about nature or animals?"
                },
                required: true,
                options: [
                    { ar: "أبداً", en: "Never", value: 1 },
                    { ar: "ربما مرة واحدة", en: "Maybe once", value: 2 },
                    { ar: "بين الحين والآخر", en: "From time to time", value: 3 },
                    { ar: "بشكل متكرر", en: "Frequently", value: 4 },
                    { ar: "دائماً", en: "Always", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            },
            {
                type: 'multiple_choice',
                text: {
                    ar: "هل من المهم بالنسبة لك قضاء بعض الوقت مع الطبيعة؟",
                    en: "Is it important for you to spend some time with nature?"
                },
                required: true,
                options: [
                    { ar: "لا", en: "No", value: 1 },
                    { ar: "قليلاً", en: "A little", value: 2 },
                    { ar: "إلى حد ما", en: "Somewhat", value: 3 },
                    { ar: "بقدر كافي", en: "Enough", value: 4 },
                    { ar: "كثيراً جداً", en: "Very much", value: 5 },
                    { ar: "لا أعرف", en: "I don't know", value: 0 }
                ],
                category: "naturalist"
            }
        ],
        scoring: {
            calculateScore: (answers, questions) => {
                const categoryScores = {
                    musical: 0,
                    bodily: 0,
                    logical: 0,
                    spatial: 0,
                    linguistic: 0,
                    intrapersonal: 0,
                    interpersonal: 0,
                    naturalist: 0
                };

                questions.forEach((question, index) => {
                    const answer = answers[index];
                    if (answer !== undefined && question.category) {
                        const option = question.options[answer];
                        if (option && option.value !== undefined) {
                            categoryScores[question.category] += option.value;
                        }
                    }
                });

                // تحويل الدرجات إلى نسب مئوية
                const maxScores = {
                    musical: 55, // 11 سؤال × 5
                    bodily: 50,  // 10 أسئلة × 5
                    logical: 45, // 9 أسئلة × 5
                    spatial: 50, // 10 أسئلة × 5
                    linguistic: 65, // 13 سؤال × 5
                    intrapersonal: 65, // 13 سؤال × 5
                    interpersonal: 70, // 14 سؤال × 5
                    naturalist: 65 // 13 سؤال × 5
                };

                const percentages = {};
                Object.keys(categoryScores).forEach(category => {
                    percentages[category] = Math.round((categoryScores[category] / maxScores[category]) * 100);
                });

                return percentages;
            },
            interpretation: (scores, language) => {
                const intelligenceTypes = {
                    musical: {
                        ar: { name: 'الذكاء الموسيقي - الإيقاعي', desc: 'القدرة على إدراك الأنماط الموسيقية والإيقاعية وإنشائها' },
                        en: { name: 'Musical-Rhythmic Intelligence', desc: 'Ability to perceive and create musical patterns and rhythms' }
                    },
                    bodily: {
                        ar: { name: 'الذكاء الجسمي - الحركي', desc: 'القدرة على استخدام الجسم بمهارة والتعبير من خلال الحركة' },
                        en: { name: 'Bodily-Kinesthetic Intelligence', desc: 'Ability to use body skillfully and express through movement' }
                    },
                    logical: {
                        ar: { name: 'الذكاء المنطقي - الرياضي', desc: 'القدرة على التفكير المنطقي وحل المشكلات الرياضية' },
                        en: { name: 'Logical-Mathematical Intelligence', desc: 'Ability to think logically and solve mathematical problems' }
                    },
                    spatial: {
                        ar: { name: 'الذكاء المكاني - البصري', desc: 'القدرة على إدراك العالم البصري المكاني بدقة' },
                        en: { name: 'Visual-Spatial Intelligence', desc: 'Ability to perceive the visual-spatial world accurately' }
                    },
                    linguistic: {
                        ar: { name: 'الذكاء اللغوي - اللفظي', desc: 'القدرة على استخدام اللغة بفعالية والتعبير عن الأفكار' },
                        en: { name: 'Verbal-Linguistic Intelligence', desc: 'Ability to use language effectively and express ideas' }
                    },
                    intrapersonal: {
                        ar: { name: 'الذكاء الشخصي - الذاتي', desc: 'القدرة على فهم الذات ومشاعرها ودوافعها' },
                        en: { name: 'Intrapersonal Intelligence', desc: 'Ability to understand oneself and ones feelings and motivations' }
                    },
                    interpersonal: {
                        ar: { name: 'الذكاء الاجتماعي - التفاعلي', desc: 'القدرة على فهم الآخرين والتفاعل معهم بفعالية' },
                        en: { name: 'Interpersonal Intelligence', desc: 'Ability to understand others and interact effectively with them' }
                    },
                    naturalist: {
                        ar: { name: 'الذكاء الطبيعي - البيئي', desc: 'القدرة على فهم الطبيعة والكائنات الحية' },
                        en: { name: 'Naturalist Intelligence', desc: 'Ability to understand nature and living organisms' }
                    }
                };

                // تحويل النتائج إلى مصفوفة وترتيبها تنازلياً
                const results = Object.keys(scores).map(category => ({
                    type: intelligenceTypes[category][language].name,
                    score: scores[category],
                    description: intelligenceTypes[category][language].desc,
                    category: category
                })).sort((a, b) => b.score - a.score);

                const topThree = results.slice(0, 3);
                
                return {
                    level: language === 'ar' ? 'تحليل الذكاءات المتعددة' : 'Multiple Intelligences Analysis',
                    desc: language === 'ar' 
                        ? `أعلى ذكاءاتك هي: ${topThree[0].type} (${topThree[0].score}%)، ${topThree[1].type} (${topThree[1].score}%)، ${topThree[2].type} (${topThree[2].score}%)` 
                        : `Your highest intelligences are: ${topThree[0].type} (${topThree[0].score}%), ${topThree[1].type} (${topThree[1].score}%), ${topThree[2].type} (${topThree[2].score}%)`,
                    details: results,
                    chartData: {
                        labels: results.map(r => r.type),
                        scores: results.map(r => r.score),
                        colors: ['#4285F4', '#34A853', '#FBBC05', '#EA4335', '#8E44AD', '#16A085', '#2C3E50', '#E74C3C']
                    }
                };
            }
        }
    },
    // ========== مقياس تقدير الذات (روزن بيرج) ==========
    {
        id: 'rosenberg-self-esteem',
        title: { ar: 'مقياس تقدير الذات - روزن بيرج', en: 'Rosenberg Self-Esteem Scale' },
        description: { 
            ar: 'مقياس عالمي معتمد لتقييم مستوى تقدير الذات والثقة بالنفس',
            en: 'Internationally certified scale to assess self-esteem and self-confidence levels'
        },
        category: 'women',
        icon: 'fas fa-star',
        questions: 10,
        time: '3-5',
        rating: 4,
        reviews: 215,
        questions: [
            { 
                ar: "في المجمل أنا راض على نفسي", 
                en: "On the whole, I am satisfied with myself" 
            },
            { 
                ar: "أعتقد أحياناً أنني جيد في كل شيء", 
                en: "At times I think I am good at everything" 
            },
            { 
                ar: "أشعر بأن لدي أشياء مميزة في حياتي", 
                en: "I feel that I have special things in my life" 
            },
            { 
                ar: "أستطيع أن أعمل أشياء جيدة كمعظم الناس", 
                en: "I can do good things like most people" 
            },
            { 
                ar: "أشعر بأنه ليس لدي الكثير لأخجل منه", 
                en: "I feel I don't have much to be ashamed of" 
            },
            { 
                ar: "بالتأكيد أشعر بعدم الفائدة في بعض الأحيان", 
                en: "I certainly feel useless at times" 
            },
            { 
                ar: "أشعر بأنني شخص له قيمة أو على الأقل متساوي مع الآخرين", 
                en: "I feel that I am a person of worth, at least on an equal plane with others" 
            },
            { 
                ar: "أرغب أن أحترم نفسي أكثر", 
                en: "I wish I could have more respect for myself" 
            },
            { 
                ar: "بشكل عام أميل إلى الشعور بأنني فاشل", 
                en: "All in all, I am inclined to feel that I am a failure" 
            },
            { 
                ar: "لدي اتجاه إيجابي تجاه نفسي", 
                en: "I take a positive attitude toward myself" 
            }
        ],
        options: [
            { ar: "غير موافق بشدة", en: "Strongly disagree" },
            { ar: "غير موافق بدرجة بسيطة", en: "Slightly disagree" },
            { ar: "بين الموافقة وعدمها", en: "Neutral" },
            { ar: "موافق بدرجة بسيطة", en: "Slightly agree" },
            { ar: "موافق بشدة", en: "Strongly agree" }
        ],
        // الأسئلة 2، 6، 8، 9 عكسية (reverse scored)
        scores: (questionIndex) => {
            const reverseQuestions = [1, 5, 7, 8]; // indices of reverse-scored questions (0-based)
            return reverseQuestions.includes(questionIndex) ? [4, 3, 2, 1, 0] : [0, 1, 2, 3, 4];
        },
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { 
                        level: 'تقدير ذات منخفض جداً', 
                        desc: 'مستوى تقدير الذات منخفض جداً ويحتاج إلى دعم فوري. ننصح بمراجعة أخصائي نفسي.' 
                    },
                    { 
                        level: 'تقدير ذات منخفض', 
                        desc: 'مستوى تقدير الذات منخفض ويحتاج للعمل على تعزيز الثقة بالنفس وتطوير صورة ذاتية إيجابية.' 
                    },
                    { 
                        level: 'تقدير ذات متوسط', 
                        desc: 'مستوى تقدير الذات مقبول ولكن يمكن تحسينه. العمل على تعزيز الثقة بالنفس سيفيدك.' 
                    },
                    { 
                        level: 'تقدير ذات مرتفع', 
                        desc: 'تمتلك تقديراً ذاتياً صحياً ومستوى جيداً من الثقة بالنفس. هذا يساعدك في مواجهة تحديات الحياة.' 
                    },
                    { 
                        level: 'تقدير ذات مرتفع جداً', 
                        desc: 'تمتلك تقديراً ذاتياً ممتازاً ومستوى عالياً من الثقة بالنفس. هذا مؤشر ممتاز على صحتك النفسية.' 
                    }
                ],
                en: [
                    { 
                        level: 'Very Low Self-Esteem', 
                        desc: 'Self-esteem level is very low and requires immediate support. We recommend seeing a psychologist.' 
                    },
                    { 
                        level: 'Low Self-Esteem', 
                        desc: 'Self-esteem level is low and needs work to boost self-confidence and develop a positive self-image.' 
                    },
                    { 
                        level: 'Moderate Self-Esteem', 
                        desc: 'Self-esteem level is acceptable but can be improved. Working on boosting self-confidence will benefit you.' 
                    },
                    { 
                        level: 'High Self-Esteem', 
                        desc: 'You have healthy self-esteem and a good level of self-confidence. This helps you face life challenges.' 
                    },
                    { 
                        level: 'Very High Self-Esteem', 
                        desc: 'You have excellent self-esteem and a high level of self-confidence. This is an excellent indicator of your mental health.' 
                    }
                ]
            };
            
            if (score <= 15) return results[lang][0];
            if (score <= 25) return results[lang][1];
            if (score <= 30) return results[lang][2];
            if (score <= 35) return results[lang][3];
            return results[lang][4];
        }
    },

    // ========== قائمة أرون بيك للاكتئاب BDI-II ==========
    {
        id: 'beck-depression',
        title: { ar: 'قائمة أرون بيك للاكتئاب BDI-II', en: 'Beck Depression Inventory BDI-II' },
        description: { 
            ar: 'مقياس معتمد عالمياً لتقييم شدة الاكتئاب من خلال 21 بنداً تغطي الأعراض المعرفية والجسدية',
            en: 'Internationally certified scale to assess depression severity through 21 items covering cognitive and physical symptoms'
        },
        category: 'women',
        icon: 'fas fa-brain',
        questions: 21,
        time: '10-15',
        rating: 4,
        reviews: 156,
        questions: [
            { 
                ar: "الحزن", 
                en: "Sadness",
                options: [
                    { ar: "لا أشعر بالحزن", en: "I do not feel sad" },
                    { ar: "أشعر بالحزن معظم الوقت", en: "I feel sad much of the time" },
                    { ar: "أشعر بالحزن طوال الوقت", en: "I am sad all the time" },
                    { ar: "أشعر بالحزن لدرجة لا أستطيع تحمل ذلك", en: "I am so sad or unhappy that I can't stand it" }
                ]
            },
            { 
                ar: "التشاؤم", 
                en: "Pessimism",
                options: [
                    { ar: "لم تضعف همتي فيما يتعلق بمستقبلي", en: "I am not discouraged about my future" },
                    { ar: "أشعر بضعف همتي فيما يتعلق بمستقبلي بطريقة أكثر مما تعودت", en: "I feel more discouraged about my future than I used to be" },
                    { ar: "أتوقع الا تسير الأمور بشكل جيد بالنسبة لي", en: "I do not expect things to work out for me" },
                    { ar: "أشعر بأنه لا أمل لي في المستقبل وأنه سوف تزداد الأمور سوءا", en: "I feel my future is hopeless and will only get worse" }
                ]
            },
            { 
                ar: "الفشل السابق", 
                en: "Past Failure",
                options: [
                    { ar: "لا أشعر بأني شخص فاشل", en: "I do not feel like a failure" },
                    { ar: "لقد فشلت أكثر مما ينبغي", en: "I have failed more than I should have" },
                    { ar: "كلما نظرت إلى حياتي السابقة أرى الكثير من الفشل", en: "As I look back, I see a lot of failures" },
                    { ar: "أشعر بأني شخص فاشل تمامًا", en: "I feel I am a complete failure as a person" }
                ]
            },
            { 
                ar: "فقدان الاستمتاع بالحياة", 
                en: "Loss of Pleasure",
                options: [
                    { ar: "أستمتع بالحياة بنفس قدر استمتاعي بها من قبل", en: "I get as much pleasure as I ever did from the things I enjoy" },
                    { ar: "لا أستمتع بالحياة بنفس القدر الذي اعتدت عليه", en: "I don't enjoy things as much as I used to" },
                    { ar: "أحصل على قدر قليل جدًا من الاستمتاع بالحياة مما تعودت عليه من قبل", en: "I get very little pleasure from the things I used to enjoy" },
                    { ar: "لا أستطيع الحصول على أي استمتاع بالحياة كما تعودت أن أستمتع من قبل", en: "I can't get any pleasure from the things I used to enjoy" }
                ]
            },
            { 
                ar: "مشاعر الإثم (تأنيب الضمير)", 
                en: "Guilty Feelings",
                options: [
                    { ar: "لا أشعر بالذنب", en: "I don't feel particularly guilty" },
                    { ar: "أشعر بالذنب حيال العديد من الأشياء التي قمت بها، أو أشياء كان يجب أن أقوم بها ولم أقم بها", en: "I feel guilty over many things I have done or should have done" },
                    { ar: "أشعر بالذنب في معظم الوقت", en: "I feel quite guilty most of the time" },
                    { ar: "أشعر بالذنب في كل الأوقات", en: "I feel guilty all of the time" }
                ]
            },
            { 
                ar: "الشعور بالتعرض للعقاب أو الأذى", 
                en: "Feeling of Punishment",
                options: [
                    { ar: "لا أشعر بأني يمكن أن أتعرض للعقاب أو للأذى", en: "I don't feel I am being punished" },
                    { ar: "أشعر بأني يمكن أن أتعرض قليلًا للعقاب أو للأذى", en: "I feel I may be punished" },
                    { ar: "أشعر بأني سوف أتعرض كثيراً للعقاب أو للأذى", en: "I expect to be punished" },
                    { ar: "أشعر بأني سوف أتعرض دائمًا للعقاب أو للأذى", en: "I feel I am being punished" }
                ]
            },
            { 
                ar: "عدم حب الذات", 
                en: "Self-Dislike",
                options: [
                    { ar: "شعوري نحو نفسي عادي", en: "I feel the same about myself as ever" },
                    { ar: "فقدت الثقة في نفسي", en: "I have lost confidence in myself" },
                    { ar: "أصبت بخيبة أمل في نفسي", en: "I am disappointed in myself" },
                    { ar: "لا أحب نفسي", en: "I dislike myself" }
                ]
            },
            { 
                ar: "نقد الذات ولومها", 
                en: "Self-Criticalness",
                options: [
                    { ar: "لا انتقد ولا ألوم نفسي", en: "I don't criticize or blame myself more than usual" },
                    { ar: "انتقد وألوم نفسي أكثر مما تعودت", en: "I am more critical of myself than I used to be" },
                    { ar: "انتقد وألوم نفسي على كل أخطائي", en: "I criticize myself for all of my faults" },
                    { ar: "انتقد وألوم نفسي على كل ما يحدث بسببي من أشياء سيئة", en: "I blame myself for everything bad that happens" }
                ]
            },
            { 
                ar: "الأفكار أو الرغبات الانتحارية", 
                en: "Suicidal Thoughts",
                options: [
                    { ar: "ليس لدي أي أفكار للانتحار", en: "I don't have any thoughts of killing myself" },
                    { ar: "لدي أفكار للانتحار ولكن لا يمكنني تنفيذها", en: "I have thoughts of killing myself, but I would not carry them out" },
                    { ar: "أريد ان انتحر", en: "I would like to kill myself" },
                    { ar: "قد انتحر لو سمحت لي الفرصة", en: "I would kill myself if I had the chance" }
                ]
            },
            { 
                ar: "البكاء", 
                en: "Crying",
                options: [
                    { ar: "لا أبكي أكثر مما تعودت", en: "I don't cry any more than usual" },
                    { ar: "أشعر بالرغبة في البكاء", en: "I cry more than I used to" },
                    { ar: "أبكي أكثر مما تعودت", en: "I cry over every little thing" },
                    { ar: "أبكي بكثرة جدًا", en: "I feel like crying, but I can't" }
                ]
            },
            { 
                ar: "الهيجان والإثارة (عدم الاستقرار)", 
                en: "Agitation",
                options: [
                    { ar: "أشعر بالهيجان والإثارة بدرجة عادية", en: "I am no more restless or wound up than usual" },
                    { ar: "أشعر بالهيجان والإثارة أكثر مما تعودت", en: "I feel more restless or wound up than usual" },
                    { ar: "اتهيج وأثور إلى درجة أنه من الصعب علي البقاء مستقراً", en: "I am so restless or agitated that it's hard to stay still" },
                    { ar: "اتهيج وأثور إلى درجة تدفعني إلى الحركة أو إلى فعل شيء ما", en: "I am so restless or agitated that I have to keep moving or doing something" }
                ]
            },
            { 
                ar: "فقدان الاهتمام أو الانسحاب الاجتماعي", 
                en: "Loss of Interest",
                options: [
                    { ar: "لم أفقد الاهتمام بالآخرين أو بالأنشطة العادية", en: "I have not lost interest in other people or activities" },
                    { ar: "أنا قليل الاهتمام بالآخرين أو الأنشطة العادية", en: "I am less interested in other people or things than before" },
                    { ar: "فقدت معظم اهتمامي بالآخرين وبكثير من الأمور", en: "I have lost most of my interest in other people or things" },
                    { ar: "من الصعب علي أن أهتم بشيء", en: "It's hard to get interested in anything" }
                ]
            },
            { 
                ar: "التردد في اتخاذ القرارات", 
                en: "Indecisiveness",
                options: [
                    { ar: "اتخذ القرارات بنفس كفاءتي التي تعودت عليها", en: "I make decisions about as well as ever" },
                    { ar: "أجد صعوبة في اتخاذ القرارات", en: "I find it more difficult to make decisions than usual" },
                    { ar: "لدي صعوبة في اتخاذ القرارات أكثر بكثير مما تعودت عليه", en: "I have much greater difficulty in making decisions than I used to" },
                    { ar: "لا أستطيع اتخاذ القرارات", en: "I have trouble making any decisions" }
                ]
            },
            { 
                ar: "انعدام القيمة", 
                en: "Worthlessness",
                options: [
                    { ar: "لا أشعر أني عديم القيمة", en: "I do not feel I am worthless" },
                    { ar: "أنا لست ذا قيمة كما تعودت أن أكون", en: "I don't consider myself as worthwhile and useful as I used to" },
                    { ar: "أشعر أني عديم القيمة عندما أقارن نفسي بالآخرين", en: "I feel more worthless as compared to other people" },
                    { ar: "أشعر أني عديم القيمة تمامًا", en: "I feel utterly worthless" }
                ]
            },
            { 
                ar: "فقدان الطاقة على العمل", 
                en: "Loss of Energy",
                options: [
                    { ar: "لدي نفس القدر من الطاقة كما تعودت", en: "I have as much energy as ever" },
                    { ar: "لدي قدر من الطاقة أقل مما تعودت", en: "I have less energy than I used to have" },
                    { ar: "ليس لدي طاقة كافية لعمل الكثير من الأشياء", en: "I don't have enough energy to do very much" },
                    { ar: "ليس لدي طاقة لعمل أي شيء", en: "I don't have enough energy to do anything" }
                ]
            },
            { 
                ar: "تغيرات في نظام النوم", 
                en: "Sleep Changes",
                options: [
                    { ar: "لم يحدث لي أي تغير في نظام نومي", en: "I have not experienced any change in my sleeping pattern" },
                    { ar: "أنام أكثر مما تعودت إلى حد ما", en: "I sleep somewhat more than usual" },
                    { ar: "أنام أقل مما تعودت إلى حد ما", en: "I sleep somewhat less than usual" },
                    { ar: "أنام أكثر مما تعودت بشكل كبير", en: "I sleep a lot more than usual" },
                    { ar: "أنام أقل مما تعودت بشكل كبير", en: "I sleep a lot less than usual" },
                    { ar: "أنام معظم اليوم", en: "I sleep most of the day" },
                    { ar: "استيقظ من نومي مبكراً ساعة أو أكثر ولا أستطيع أن أعود مرة أخرى", en: "I wake up 1-2 hours early and can't get back to sleep" }
                ]
            },
            { 
                ar: "القابلية للغضب أو الانزعاج", 
                en: "Irritability",
                options: [
                    { ar: "أغضب بدرجة عادية", en: "I am no more irritable than usual" },
                    { ar: "أغضب أكثر مما تعودت", en: "I am more irritable than usual" },
                    { ar: "أغضب أكبر بكثير مما تعودت", en: "I am much more irritable than usual" },
                    { ar: "أجد نفسي في حالة غضب طوال الوقت", en: "I am irritable all the time" }
                ]
            },
            { 
                ar: "تغيرات في الشهية", 
                en: "Appetite Changes",
                options: [
                    { ar: "لم يحدث أي تغير في شهيتي", en: "I have not experienced any change in my appetite" },
                    { ar: "شهيتي أقل مما تعودت إلى حد ما", en: "My appetite is somewhat less than usual" },
                    { ar: "شهيتي أكبر مما تعودت إلى حد ما", en: "My appetite is somewhat greater than usual" },
                    { ar: "شهيتي أقل كثيراً مما تعودت", en: "My appetite is much less than usual" },
                    { ar: "شهيتي أكبر كثيراً مما تعودت", en: "My appetite is much greater than usual" },
                    { ar: "ليست لدي شهية على الاطلاق", en: "I have no appetite at all" },
                    { ar: "لدي رغبة قوية إلى الطعام طوال الوقت", en: "I crave food all the time" }
                ]
            },
            { 
                ar: "صعوبة التركيز", 
                en: "Concentration Difficulty",
                options: [
                    { ar: "أستطيع التركيز بكفاءة كما تعودت", en: "I can concentrate as well as ever" },
                    { ar: "لا أستطيع التركيز بنفس الكفاءة كما تعودت", en: "I can't concentrate as well as usual" },
                    { ar: "من الصعب عليا أن أركز عقلي على أي شيء لمدة طويلة", en: "It's hard to keep my mind on anything for very long" },
                    { ar: "أجد نفسي غير قادر على التركيز على أي شيء", en: "I find I can't concentrate on anything" }
                ]
            },
            { 
                ar: "الإرهاق والإجهاد", 
                en: "Tiredness/Fatigue",
                options: [
                    { ar: "لست أكثر إرهاقا أو إجهادا مما تعودت", en: "I am no more tired or fatigued than usual" },
                    { ar: "أصاب بالإرهاق أو الإجهاد عند عمل الكثير من الأشياء التي أعتدت عليها", en: "I get more tired or fatigued more easily than usual" },
                    { ar: "يعوقني الإرهاق أو الاجهاد من عمل الكثير من الأشياء التي أعتدت عليها", en: "I am too tired or fatigued to do a lot of the things I used to do" },
                    { ar: "أنا مرهق ومجهد جداً، بحيث أجد صعوبة بعمل معظم الأشياء التي أعتدت عليها", en: "I am too tired or fatigued to do most of the things I used to do" }
                ]
            },
            { 
                ar: "فقدان الاهتمام بالجنس", 
                en: "Loss of Interest in Sex",
                options: [
                    { ar: "أن اهتمامي بالجنس عادي في هذه الأيام", en: "I have not noticed any recent change in my interest in sex" },
                    { ar: "أنا أقل اهتماماً بالجنس في هذه الأيام مما تعودت", en: "I am less interested in sex than I used to be" },
                    { ar: "أنا أقل اهتماماً بالجنس بدرجة كبيرة في هذه الأيام", en: "I am much less interested in sex now" },
                    { ar: "فقدت الاهتمام بالجنس تمامًا", en: "I have lost interest in sex completely" }
                ]
            }
        ],
        scores: (questionIndex) => {
            // الأسئلة لها درجات مختلفة حسب عدد الخيارات
            const questionScores = {
                15: [0, 1, 2, 3, 4, 5, 6], // سؤال النوم (7 خيارات)
                17: [0, 1, 2, 3, 4, 5, 6]  // سؤال الشهية (7 خيارات)
            };
            
            return questionScores[questionIndex] || [0, 1, 2, 3]; // معظم الأسئلة 4 خيارات
        },
        interpretation: (score, lang, gender = 'female') => {
            const results = {
                female: {
                    ar: [
                        { level: 'لا يوجد اكتئاب', desc: 'مستوى الاكتئاب ضمن المعدل الطبيعي. هذا مؤشر جيد على صحتك النفسية.' },
                        { level: 'اكتئاب بسيط', desc: 'هناك مؤشرات على وجود اكتئاب بسيط. قد تحتاج لمتابعة حالتك.' },
                        { level: 'اكتئاب متوسط', desc: 'هناك اكتئاب متوسط يؤثر على حياتك. ننصح باستشارة أخصائي نفسي.' },
                        { level: 'اكتئاب شديد', desc: 'مستوى الاكتئاب شديد ويحتاج لتدخل فوري. ننصح بمراجعة أخصائي نفسي بأسرع وقت.' }
                    ],
                    en: [
                        { level: 'No Depression', desc: 'Depression level is within normal range. This is a good indicator of your mental health.' },
                        { level: 'Mild Depression', desc: 'There are indicators of mild depression. You may need to monitor your condition.' },
                        { level: 'Moderate Depression', desc: 'There is moderate depression affecting your life. We recommend consulting a psychologist.' },
                        { level: 'Severe Depression', desc: 'Depression level is severe and requires immediate intervention. We recommend seeing a psychologist as soon as possible.' }
                    ]
                },
                male: {
                    ar: [
                        { level: 'لا يوجد اكتئاب', desc: 'مستوى الاكتئاب ضمن المعدل الطبيعي. هذا مؤشر جيد على صحتك النفسية.' },
                        { level: 'اكتئاب بسيط', desc: 'هناك مؤشرات على وجود اكتئاب بسيط. قد تحتاج لمتابعة حالتك.' },
                        { level: 'اكتئاب متوسط', desc: 'هناك اكتئاب متوسط يؤثر على حياتك. ننصح باستشارة أخصائي نفسي.' },
                        { level: 'اكتئاب شديد', desc: 'مستوى الاكتئاب شديد ويحتاج لتدخل فوري. ننصح بمراجعة أخصائي نفسي بأسرع وقت.' }
                    ],
                    en: [
                        { level: 'No Depression', desc: 'Depression level is within normal range. This is a good indicator of your mental health.' },
                        { level: 'Mild Depression', desc: 'There are indicators of mild depression. You may need to monitor your condition.' },
                        { level: 'Moderate Depression', desc: 'There is moderate depression affecting your life. We recommend consulting a psychologist.' },
                        { level: 'Severe Depression', desc: 'Depression level is severe and requires immediate intervention. We recommend seeing a psychologist as soon as possible.' }
                    ]
                }
            };

            const genderResults = results[gender] || results.female;
            
            if (gender === 'female') {
                if (score <= 20) return genderResults[lang][0];
                if (score <= 29) return genderResults[lang][1];
                if (score <= 38) return genderResults[lang][2];
                return genderResults[lang][3];
            } else {
                if (score <= 16) return genderResults[lang][0];
                if (score <= 24) return genderResults[lang][1];
                if (score <= 32) return genderResults[lang][2];
                return genderResults[lang][3];
            }
        }
    },

    // ========== مقياس كرب ما بعد الصدمة (PTSD) ==========
    {
        id: 'ptsd',
        title: { ar: 'مقياس كرب ما بعد الصدمة', en: 'Post-Traumatic Stress Disorder Scale' },
        description: { 
            ar: 'تقييم أعراض اضطراب كرب ما بعد الصدمة وفقاً لمعايير DSM-5',
            en: 'Assessment of post-traumatic stress disorder symptoms according to DSM-5 criteria'
        },
        category: 'women',
        icon: 'fas fa-first-aid',
        questions: 20,
        time: '8-10',
        rating: 4,
        reviews: 95,
        questions: [
            { 
                ar: "تراودني ذكريات متكررة، مزعجة مرتبطة بالخبرة الصادمة التي مررت بها", 
                en: "I have repeated, disturbing memories of the traumatic experience I went through" 
            },
            { 
                ar: "تراودني أحلام مزعجة متكررة مرتبطة بالخبرة الصادمة التي مررت بها", 
                en: "I have repeated, disturbing dreams related to the traumatic experience I went through" 
            },
            { 
                ar: "تنتابني مشاعر فجائية بأن الخبرة الصادمة ستحدث مرة أخرى", 
                en: "I suddenly feel as if the traumatic experience is happening again" 
            },
            { 
                ar: "أشعر بالضيق الشديد عندما أتعرض لموقف يذكرني بالخبرة الصادمة", 
                en: "I feel very upset when exposed to situations that remind me of the traumatic experience" 
            },
            { 
                ar: "أشعر بتغيرات جسدية شديدة: كزيادة دقات القلب أو صعوبة في التنفس أو التعرق عندما أتعرض لموقف يذكرني بالخبرة الصادمة", 
                en: "I experience strong physical reactions like increased heart rate, difficulty breathing, or sweating when exposed to situations that remind me of the traumatic experience" 
            },
            { 
                ar: "أتجنب الذكريات أو الأفكار أو المشاعر المرتبطة بالخبرة الصادمة", 
                en: "I avoid memories, thoughts, or feelings associated with the traumatic experience" 
            },
            { 
                ar: "أتجنب الذكريات الخارجية للخبرة الصادمة: كالأشخاص أو الأماكن أو المحادثات أو الأنشطة أو الأشياء أو المواقف", 
                en: "I avoid external reminders of the traumatic experience such as people, places, conversations, activities, objects, or situations" 
            },
            { 
                ar: "أجد صعوبة في تذكر أجزاء مهمة من الخبرة الصادمة", 
                en: "I have difficulty remembering important parts of the traumatic experience" 
            },
            { 
                ar: "تنتابني أفكار سلبية قوية عن نفسي أو عن الآخرين أو عن العالم، مثل: أنا شخص سيء لا يمكن الوثوق بأي شخص، العالم خطير تماماً", 
                en: "I have strong negative thoughts about myself, others, or the world, such as: I am a bad person, no one can be trusted, the world is completely dangerous" 
            },
            { 
                ar: "ألوم نفسي أو أي شخص آخر على حدوث الخبرة الصادمة أو على ما يحدث بعد ذلك", 
                en: "I blame myself or someone else for the traumatic experience or for what happened afterwards" 
            },
            { 
                ar: "تنتابني مشاعر سلبية قوية: كالخوف أو الرعب أو الغضب أو الشعور بالذنب أو الشعور بالعار", 
                en: "I experience strong negative feelings such as fear, terror, anger, guilt, or shame" 
            },
            { 
                ar: "أفتقد الاهتمام بالأنشطة التي كنت استمتع بها", 
                en: "I have lost interest in activities I used to enjoy" 
            },
            { 
                ar: "أشعر بالبعد أو الانقطاع عن الآخرين", 
                en: "I feel distant or cut off from other people" 
            },
            { 
                ar: "أجد صعوبة في تجربة مشاعري الإيجابية، مثل: عدم القدرة على الشعور بالسعادة أو بمحبة الناس المقربين مني أو الشعور بالرضا", 
                en: "I have difficulty experiencing positive feelings, such as inability to feel happiness, love for close people, or satisfaction" 
            },
            { 
                ar: "ينتابني سلوك متهور أو نوبات غضب أو تصرف عدواني", 
                en: "I engage in reckless behavior, anger outbursts, or aggressive behavior" 
            },
            { 
                ar: "أتحمل الكثير من المخاطر، وأقوم بأشياء يمكن أن تسبب لي الأذى", 
                en: "I take many risks and do things that could harm me" 
            },
            { 
                ar: "تنتابني يقظة زائدة أو حذر شديد", 
                en: "I experience hypervigilance or extreme caution" 
            },
            { 
                ar: "أشعر بالتوتر وسرعة الانفعال", 
                en: "I feel tense and easily irritated" 
            },
            { 
                ar: "أجد صعوبة في التركيز", 
                en: "I have difficulty concentrating" 
            },
            { 
                ar: "أجد صعوبة في الدخول بالنوم أو الاستمرار به", 
                en: "I have difficulty falling asleep or staying asleep" 
            }
        ],
        options: [
            { ar: "أبداً", en: "Not at all" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { 
                        level: 'لا يوجد اضطراب', 
                        desc: 'لا توجد مؤشرات واضحة لاضطراب كرب ما بعد الصدمة. هذا مؤشر جيد على صحتك النفسية.' 
                    },
                    { 
                        level: 'يوجد اضطراب خفيف', 
                        desc: 'هناك مؤشرات على وجود اضطراب خفيف في كرب ما بعد الصدمة. قد تحتاج لمتابعة حالتك.' 
                    },
                    { 
                        level: 'اضطراب متوسط', 
                        desc: 'هناك اضطراب متوسط في كرب ما بعد الصدمة يؤثر على حياتك. ننصح باستشارة أخصائي نفسي.' 
                    },
                    { 
                        level: 'اضطراب شديد', 
                        desc: 'هناك اضطراب شديد في كرب ما بعد الصدمة ويحتاج لتدخل فوري. ننصح بمراجعة أخصائي نفسي بأسرع وقت.' 
                    },
                    { 
                        level: 'اضطراب شديد جداً', 
                        desc: 'هناك اضطراب شديد جداً في كرب ما بعد الصدمة ويتطلب رعاية فورية ومكثفة من أخصائي نفسي.' 
                    }
                ],
                en: [
                    { 
                        level: 'No Disorder', 
                        desc: 'There are no clear indicators of post-traumatic stress disorder. This is a good indicator of your mental health.' 
                    },
                    { 
                        level: 'Mild Disorder', 
                        desc: 'There are indicators of mild post-traumatic stress disorder. You may need to monitor your condition.' 
                    },
                    { 
                        level: 'Moderate Disorder', 
                        desc: 'There is moderate post-traumatic stress disorder that affects your life. We recommend consulting a psychologist.' 
                    },
                    { 
                        level: 'Severe Disorder', 
                        desc: 'There is severe post-traumatic stress disorder that requires immediate intervention. We recommend seeing a psychologist as soon as possible.' 
                    },
                    { 
                        level: 'Very Severe Disorder', 
                        desc: 'There is very severe post-traumatic stress disorder that requires immediate and intensive care from a psychologist.' 
                    }
                ]
            };
            
            if (score <= 15) return results[lang][0];
            if (score <= 31) return results[lang][1];
            if (score <= 47) return results[lang][2];
            if (score <= 63) return results[lang][3];
            return results[lang][4];
        }
    },

    // ========== مقياس الاكتئاب  ==========
    {
        id: 'depression',
        title: {ar:'مقياس الاكتئاب',en:'Depression Scale'},
        description: { 
            ar: 'تقييم لمشاعر الحزن والاكتئاب ومدى تأثيرها على حياتك اليومية',
            en: 'Assessment of sadness and depression feelings and their impact on your daily life'
        },
        category: 'women',
        icon: 'fas fa-cloud',
        questions: 9,
        time: '4-6',
        rating: 4,
        reviews: 156,
        questions: [
            { ar: "قلة الاهتمام أو المتعة في doing things", en: "Little interest or pleasure in doing things" },
            { ar: "الشعور بالحزن أو الاكتئاب أو اليأس", en: "Feeling down, depressed, or hopeless" },
            { ar: "صعوبة في النوم أو النوم أكثر من اللازم", en: "Trouble falling or staying asleep, or sleeping too much" },
            { ar: "الشعور بالتعب أو قلة الطاقة", en: "Feeling tired or having little energy" },
            { ar: "قلة الشهية أو الإفراط في الأكل", en: "Poor appetite or overeating" },
            { ar: "الشعور بعدم الرضا عن النفس", en: "Feeling bad about yourself" },
            { ar: "صعوبة في التركيز", en: "Trouble concentrating on things" },
            { ar: "التحدث أو التحرك ببطء شديد", en: "Moving or speaking so slowly that other people could have noticed" },
            { ar: "أفكار حول إيذاء النفس", en: "Thoughts that you would be better off dead" }
        ],
        options: [
            { ar: "أبداً", en: "Not at all" },
            { ar: "عدة أيام", en: "Several days" },
            { ar: "أكثر من نصف الأيام", en: "More than half the days" },
            { ar: "تقريباً كل يوم", en: "Nearly every day" }
        ],
        scores: [0, 1, 2, 3],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'اكتئاب طفيف أو معدوم', desc: 'مستوى الاكتئاب ضمن المعدل الطبيعي.' },
                    { level: 'اكتئاب بسيط', desc: 'تعاني من مستوى بسيط من الاكتئاب. قد تحتاج لمتابعة حالتك.' },
                    { level: 'اكتئاب متوسط', desc: 'الاكتئاب المتوسط يؤثر على حياتك. ننصح باستشارة أخصائي.' },
                    { level: 'اكتئاب شديد متوسط', desc: 'مستوى الاكتئاب شديد ويحتاج لتدخل فوري.' },
                    { level: 'اكتئاب شديد جداً', desc: 'مستوى الاكتئاب شديد جداً ويتطلب رعاية فورية.' }
                ],
                en: [
                    { level: 'Minimal Depression', desc: 'Depression level is within normal range.' },
                    { level: 'Mild Depression', desc: 'You experience mild depression. You may need to monitor your condition.' },
                    { level: 'Moderate Depression', desc: 'Moderate depression affects your life. We recommend consulting a specialist.' },
                    { level: 'Moderately Severe Depression', desc: 'Depression level is severe and requires immediate intervention.' },
                    { level: 'Severe Depression', desc: 'Depression level is very severe and requires immediate care.' }
                ]
            };
            
            if (score <= 4) return results[lang][0];
            if (score <= 9) return results[lang][1];
            if (score <= 14) return results[lang][2];
            if (score <= 19) return results[lang][3];
            return results[lang][4];
        }
    },

    // ========== مقياس الضغوط النفسية ==========
    {
        id: 'psychological-stress',
        title: {ar:'مقياس الضغوط النفسية',en:'Stress Scale'},
        description: { 
            ar: 'تقييم مستوى الضغوط والتوتر النفسي الذي تتعرض له في الحياة اليومية', 
            en: 'Assessment of psychological stress and tension levels in daily life'
        },
        category: 'women',
        icon: 'fas fa-weight',
        questions: 10,
        time: '5-7',
        rating: 4,
        reviews: 92,
        questions: [
            { 
                ar: "أشعر أنني غير قادر على السيطرة على الأمور المهمة في حياتي", 
                en: "I feel unable to control important things in my life" 
            },
            { 
                ar: "أشعر بالتوتر والضيق النفسي معظم الوقت", 
                en: "I feel nervous and psychologically distressed most of the time" 
            },
            { 
                ar: "أشعر بالثقة حول قدرتي على التعامل مع المشاكل الشخصية", 
                en: "I feel confident about my ability to handle personal problems" 
            },
            { 
                ar: "أشعر أن الأمور تسير في طريقها كما أخطط", 
                en: "I feel things are going my way as I plan" 
            },
            { 
                ar: "أشعر أن المسؤوليات تتراكم ولا أستطيع التعامل معها", 
                en: "I feel responsibilities are piling up and I cannot handle them" 
            },
            { 
                ar: "أشعر أنني أستطيع التحكم في الانفعالات في حياتي", 
                en: "I feel I can control the irritations in my life" 
            },
            { 
                ar: "أجد نفسي أفكر باستمرار في الأمور التي علي إنجازها", 
                en: "I find myself constantly thinking about things I need to accomplish" 
            },
            { 
                ar: "أستطيع التحكم في المضايقات اليومية بشكل جيد", 
                en: "I can handle daily hassles well" 
            },
            { 
                ar: "أشعر أنني أسيطر على حياتي بشكل كامل", 
                en: "I feel I am in complete control of my life" 
            },
            { 
                ar: "أشعر بالغضب بسبب أمور خارجة عن إرادتي", 
                en: "I feel angry about things outside my control" 
            }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { 
                        level: 'ضغوط نفسية منخفضة', 
                        desc: 'مستوى الضغوط النفسية الذي تتعرض له منخفض ومقبول. هذا مؤشر جيد على صحتك النفسية.' 
                    },
                    { 
                        level: 'ضغوط نفسية متوسطة', 
                        desc: 'تعاني من مستوى متوسط من الضغوط النفسية. حاول تطوير استراتيجيات إدارة التوتر.' 
                    },
                    { 
                        level: 'ضغوط نفسية عالية', 
                        desc: 'مستوى الضغوط النفسية مرتفع ويحتاج لاستراتيجيات فعالة للتعامل معه. ننصح بطلب الدعم.' 
                    },
                    { 
                        level: 'ضغوط نفسية شديدة', 
                        desc: 'مستوى الضغوط النفسية شديد ويتطلب تدخلاً فورياً. ننصح بمراجعة أخصائي نفسي.' 
                    }
                ],
                en: [
                    { 
                        level: 'Low Psychological Stress', 
                        desc: 'The level of psychological stress you are exposed to is low and acceptable. This is a good indicator of your mental health.' 
                    },
                    { 
                        level: 'Moderate Psychological Stress', 
                        desc: 'You experience moderate levels of psychological stress. Try to develop stress management strategies.' 
                    },
                    { 
                        level: 'High Psychological Stress', 
                        desc: 'Psychological stress level is high and requires effective coping strategies. We recommend seeking support.' 
                    },
                    { 
                        level: 'Severe Psychological Stress', 
                        desc: 'Psychological stress level is severe and requires immediate intervention. We recommend consulting a psychologist.' 
                    }
                ]
            };
            
            if (score <= 13) return results[lang][0];
            if (score <= 26) return results[lang][1];
            if (score <= 35) return results[lang][2];
            return results[lang][3];
        }
    },

    // ========== مقياس جودة الحياة ==========
    {
        id: 'quality-of-life',
        title: {ar:'مقياس جودة الحياة',en:'Quality Of Life Scale'},
        description: { 
            ar: 'قياس مستوى الرضا عن مختلف جوانب الحياة وتحديد مجالات التحسين', 
            en: 'Measuring satisfaction levels with various life aspects and identifying improvement areas'
        },
        category: 'women',
        icon: 'fas fa-home',
        questions: 8,
        time: '6-8',
        rating: 4,
        reviews: 87,
        questions: [
            { 
                ar: "ما مدى رضاك عن صحتك الجسدية؟", 
                en: "How satisfied are you with your physical health?" 
            },
            { 
                ar: "ما مدى رضاك عن صحتك النفسية والعاطفية؟", 
                en: "How satisfied are you with your mental and emotional health?" 
            },
            { 
                ar: "ما مدى رضاك عن علاقاتك الاجتماعية والأسرية؟", 
                en: "How satisfied are you with your social and family relationships?" 
            },
            { 
                ar: "ما مدى رضاك عن وضعك المالي واستقرارك الاقتصادي؟", 
                en: "How satisfied are you with your financial situation and economic stability?" 
            },
            { 
                ar: "ما مدى رضاك عن عملك أو دراستك؟", 
                en: "How satisfied are you with your work or studies?" 
            },
            { 
                ar: "ما مدى رضاك عن وقت فراغك والأنشطة الترفيهية؟", 
                en: "How satisfied are you with your leisure time and recreational activities?" 
            },
            { 
                ar: "ما مدى رضاك عن بيئتك المعيشية والسكنية؟", 
                en: "How satisfied are you with your living and housing environment?" 
            },
            { 
                ar: "ما مدى رضاك عن حياتك بشكل عام؟", 
                en: "How satisfied are you with your life in general?" 
            }
        ],
        options: [
            { ar: "غير راضٍ تماماً", en: "Very dissatisfied" },
            { ar: "غير راضٍ", en: "Dissatisfied" },
            { ar: "محايد", en: "Neutral" },
            { ar: "راضٍ", en: "Satisfied" },
            { ar: "راضٍ تماماً", en: "Very satisfied" }
        ],
        scores: [1, 2, 3, 4, 5],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { 
                        level: 'جودة حياة ممتازة', 
                        desc: 'مستوى رضاك عن حياتك ممتاز. تمتلك توازناً جيداً في مختلف جوانب الحياة.' 
                    },
                    { 
                        level: 'جودة حياة جيدة', 
                        desc: 'مستوى رضاك جيد ولكن هناك بعض المجالات التي يمكن تحسينها لرفع جودة الحياة.' 
                    },
                    { 
                        level: 'جودة حياة متوسطة', 
                        desc: 'هناك عدة مجالات في حياتك تحتاج للاهتمام والتحسين لرفع مستوى الرضا العام.' 
                    },
                    { 
                        level: 'جودة حياة تحتاج تحسين', 
                        desc: 'هناك مجالات متعددة في حياتك تحتاج للاهتمام الفوري والتحسين.' 
                    }
                ],
                en: [
                    { 
                        level: 'Excellent Quality of Life', 
                        desc: 'Your life satisfaction level is excellent. You have a good balance in various aspects of life.' 
                    },
                    { 
                        level: 'Good Quality of Life', 
                        desc: 'Your satisfaction level is good but there are some areas that can be improved to enhance quality of life.' 
                    },
                    { 
                        level: 'Moderate Quality of Life', 
                        desc: 'There are several areas in your life that need attention and improvement to raise overall satisfaction.' 
                    },
                    { 
                        level: 'Quality of Life Needs Improvement', 
                        desc: 'There are multiple areas in your life that need immediate attention and improvement.' 
                    }
                ]
            };
            
            if (score >= 35) return results[lang][0];
            if (score >= 25) return results[lang][1];
            if (score >= 15) return results[lang][2];
            return results[lang][3];
        }
    },


    // ========== مقياس فرط النشاط ونقص الانتباه ==========
    {
        id: 'child-adhd',
        title: {ar:'مقياس فرط النشاط ونقص الانتباه',en:'Adhd Scale'},
        description: { 
            ar: 'تقييم أعراض فرط النشاط ونقص الانتباه لدى الأطفال',
            en: 'Assessment of hyperactivity and attention deficit symptoms in children'
        },
        category: 'children',
        icon: 'fas fa-running',
        questions: 8,
        time: '4-6',
        rating: 4,
        reviews: 64,
        questions: [
            { ar: "هل يجد الطفل صعوبة في البقاء جالساً؟", en: "Does the child have difficulty staying seated?" },
            { ar: "هل يتشتت انتباه الطفل بسهولة؟", en: "Is the child easily distracted?" },
            { ar: "هل يجد الطفل صعوبة في انتظار دوره؟", en: "Does the child have difficulty waiting for their turn?" },
            { ar: "هل يتحدث الطفل بشكل مفرط؟", en: "Does the child talk excessively?" },
            { ar: "هل يجد الطفل صعوبة في اتباع التعليمات؟", en: "Does the child have difficulty following instructions?" },
            { ar: "هل يفقد الطفل أشياءه بشكل متكرر؟", en: "Does the child frequently lose things?" },
            { ar: "هل يتحرك الطفل بشكل دائم؟", en: "Is the child constantly moving?" },
            { ar: "هل يقاطع الطفل الآخرين؟", en: "Does the child interrupt others?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" }
        ],
        scores: [0, 1, 2, 3],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'أعراض طفيفة', desc: 'الأعراض ضمن المعدل الطبيعي.' },
                    { level: 'أعراض متوسطة', desc: 'هناك مؤشرات على وجود أعراض تحتاج للمراقبة.' },
                    { level: 'أعراض واضحة', desc: 'هناك مؤشرات واضحة تحتاج لتقييم متخصص.' }
                ],
                en: [
                    { level: 'Mild Symptoms', desc: 'Symptoms are within normal range.' },
                    { level: 'Moderate Symptoms', desc: 'There are indicators that need monitoring.' },
                    { level: 'Clear Symptoms', desc: 'There are clear indicators that need specialist evaluation.' }
                ]
            };
            
            if (score <= 8) return results[lang][0];
            if (score <= 16) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس اكتئاب الأطفال ==========
    {
        id: 'child-depression',
        title: {ar:'مقياس اكتئاب الأطفال',en:'Child Depression Scale'},
        description: { 
            ar: 'تقييم مشاعر الحزن والاكتئاب لدى الأطفال والمراهقين',
            en: 'Assessment of sadness and depression feelings in children and adolescents'
        },
        category: 'children',
        icon: 'fas fa-sad-tear',
        questions: 8,
        time: '4-5',
        rating: 4,
        reviews: 58,
        questions: [
            { ar: "كم مرة يشعر الطفل بالحزن أو البكاء؟", en: "How often does the child feel sad or cry?" },
            { ar: "كم مرة يشعر بعدم الرغبة في اللعب أو مقابلة الأصدقاء؟", en: "How often does the child feel unwilling to play or meet friends?" },
            { ar: "كم مرة يشعر بالتعب والإرهاق؟", en: "How often does the child feel tired and exhausted?" },
            { ar: "كم مرة يشعر بعدم الرغبة في تناول الطعام؟", en: "How often does the child feel unwilling to eat?" },
            { ar: "كم مرة يشعر بصعوبة في النوم؟", en: "How often does the child have difficulty sleeping?" },
            { ar: "كم مرة يشعر بعدم الرضا عن نفسه؟", en: "How often does the child feel dissatisfied with themselves?" },
            { ar: "كم مرة يشعر بصعوبة في التركيز؟", en: "How often does the child have difficulty concentrating?" },
            { ar: "كم مرة يشعر بأن لا أحد يحبه؟", en: "How often does the child feel that no one loves them?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "قليلاً", en: "A little" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "كثيراً", en: "Often" }
        ],
        scores: [0, 1, 2, 3],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'معدل طبيعي', desc: 'مشاعر الطفل ضمن المعدل الطبيعي.' },
                    { level: 'اكتئاب بسيط', desc: 'الطفل يعاني من مشاعر اكتئاب بسيطة تحتاج للمراقبة.' },
                    { level: 'اكتئاب واضح', desc: 'هناك علامات واضحة للاكتئاب تحتاج لتدخل مختص.' }
                ],
                en: [
                    { level: 'Normal Range', desc: 'The child feelings are within normal range.' },
                    { level: 'Mild Depression', desc: 'The child experiences mild depressive feelings that need monitoring.' },
                    { level: 'Clear Depression', desc: 'There are clear signs of depression that need specialist intervention.' }
                ]
            };
            
            if (score <= 8) return results[lang][0];
            if (score <= 16) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس الكفاءة الاجتماعية ==========
    {
        id: 'social-competence',
        title: {ar:'مقياس الكفاءة الاجتماعية',en:'Social Competence Scale'},
        description: { 
            ar: 'تقييم المهارات الاجتماعية والقدرة على التفاعل مع الآخرين',
            en: 'Assessment of social skills and ability to interact with others'
        },
        category: 'children',
        icon: 'fas fa-users',
        questions: 8,
        time: '4-6',
        rating: 4,
        reviews: 45,
        questions: [
            { ar: "كم مرة يبدأ الطفل المحادثات مع الآخرين؟", en: "How often does the child start conversations with others?" },
            { ar: "كم مرة يشارك الطفل في الأنشطة الجماعية؟", en: "How often does the child participate in group activities?" },
            { ar: "كم مرة يساعد الطفل الآخرين؟", en: "How often does the child help others?" },
            { ar: "كم مرة يعبر الطفل عن مشاعره بشكل مناسب؟", en: "How often does the child express feelings appropriately?" },
            { ar: "كم مرة يحل الطفل النزاعات بطريقة سلمية؟", en: "How often does the child resolve conflicts peacefully?" },
            { ar: "كم مرة يظهر الطفل التعاطف مع الآخرين؟", en: "How often does the child show empathy towards others?" },
            { ar: "كم مرة يتبع الطفل القواعد الاجتماعية؟", en: "How often does the child follow social rules?" },
            { ar: "كم مرة يتعاون الطفل مع الآخرين؟", en: "How often does the child cooperate with others?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'كفاءة عالية', desc: 'المهارات الاجتماعية ممتازة.' },
                    { level: 'كفاءة متوسطة', desc: 'المهارات الاجتماعية جيدة ولكن يمكن تطويرها.' },
                    { level: 'كفاءة منخفضة', desc: 'هناك حاجة لتطوير المهارات الاجتماعية.' }
                ],
                en: [
                    { level: 'High Competence', desc: 'Social skills are excellent.' },
                    { level: 'Moderate Competence', desc: 'Social skills are good but can be developed.' },
                    { level: 'Low Competence', desc: 'There is a need to develop social skills.' }
                ]
            };
            
            if (score >= 25) return results[lang][0];
            if (score >= 15) return results[lang][1];
            return results[lang][2];
        }
    },

    // ========== مقياس اضطرابات الشخصية ==========
    {
        id: 'personality-disorders',
        title: {ar:'مقياس اضطرابات الشخصية',en:'Personality Disorder Scale'},
        description: { 
            ar: 'مقياس متقدم لتقييم أنماط الشخصية والاضطرابات المحتملة',
            en: 'Advanced scale to assess personality patterns and potential disorders'
        },
        category: 'children',
        icon: 'fas fa-user-md',
        questions: 12,
        time: '10-12',
        rating: 4,
        reviews: 76,
        questions: [
            { ar: "هل تواجه صعوبة في الحفاظ على علاقات مستقرة؟", en: "Do you have difficulty maintaining stable relationships?" },
            { ar: "هل تعاني من تقلبات مزاجية حادة؟", en: "Do you experience severe mood swings?" },
            { ar: "هل تشعر بعدم الاستقرار في هويتك الشخصية؟", en: "Do you feel unstable in your personal identity?" },
            { ar: "هل تواجه صعوبة في السيطرة على الغضب؟", en: "Do you have difficulty controlling anger?" },
            { ar: "هل تعاني من الخوف من الهجر؟", en: "Do you suffer from fear of abandonment?" },
            { ar: "هل تشعر بالفراغ الداخلي؟", en: "Do you feel empty inside?" },
            { ar: "هل تواجه صعوبة في الثقة بالآخرين؟", en: "Do you have difficulty trusting others?" },
            { ar: "هل تعاني من الشك في نوايا الآخرين؟", en: "Do you suffer from suspicion of others intentions?" },
            { ar: "هل تشعر بعدم الارتياح في المواقف الاجتماعية؟", en: "Do you feel uncomfortable in social situations?" },
            { ar: "هل تواجه صعوبة في اتخاذ القرارات؟", en: "Do you have difficulty making decisions?" },
            { ar: "هل تعاني من الحساسية المفرطة للنقد؟", en: "Are you overly sensitive to criticism?" },
            { ar: "هل تشعر بالحاجة الدائمة للاهتمام؟", en: "Do you constantly feel the need for attention?" }
        ],
        options: [
            { ar: "لا ينطبق أبداً", en: "Never applies" },
            { ar: "ينطبق نادراً", en: "Rarely applies" },
            { ar: "ينطبق أحياناً", en: "Sometimes applies" },
            { ar: "ينطبق غالباً", en: "Often applies" },
            { ar: "ينطبق دائماً", en: "Always applies" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'نمط شخصي طبيعي', desc: 'لا توجد مؤشرات واضحة لاضطرابات الشخصية.' },
                    { level: 'سمات شخصية تحتاج مراقبة', desc: 'هناك بعض السمات التي تحتاج للمتابعة.' },
                    { level: 'احتمال وجود اضطراب شخصي', desc: 'هناك مؤشرات لاضطراب شخصي تحتاج لتقييم متخصص.' }
                ],
                en: [
                    { level: 'Normal Personality Pattern', desc: 'No clear indicators of personality disorders.' },
                    { level: 'Personality Traits Need Monitoring', desc: 'There are some traits that need follow-up.' },
                    { level: 'Possible Personality Disorder', desc: 'There are indicators of personality disorder that need specialist evaluation.' }
                ]
            };
            
            if (score <= 18) return results[lang][0];
            if (score <= 30) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس الذكاء العاطفي ==========
    {
        id: 'emotional-intelligence',
        title: {ar:'مقياس الذكاء عاطفي',en:'Emotional Intelligence Scale'},
        description: { 
            ar: 'تقييم القدرة على فهم وإدارة المشاعر والعواطف',
            en: 'Assessment of the ability to understand and manage emotions and feelings'
        },
        category: 'children',
        icon: 'fas fa-brain',
        questions: 10,
        time: '8-10',
        rating: 4,
        reviews: 89,
        questions: [
            { ar: "هل يمكنك التعرف على مشاعرك بدقة؟", en: "Can you accurately identify your emotions?" },
            { ar: "هل يمكنك التحكم في مشاعرك عند الغضب؟", en: "Can you control your emotions when angry?" },
            { ar: "هل تفهم مشاعر الآخرين بسهولة؟", en: "Do you easily understand others emotions?" },
            { ar: "هل تستطيع التعاطف مع الآخرين؟", en: "Can you empathize with others?" },
            { ar: "هل تستطيع تحفيز نفسك عند الفشل؟", en: "Can you motivate yourself after failure?" },
            { ar: "هل تستطيع بناء علاقات جيدة مع الآخرين؟", en: "Can you build good relationships with others?" },
            { ar: "هل تستطيع حل النزاعات بشكل بناء؟", en: "Can you resolve conflicts constructively?" },
            { ar: "هل تتعامل مع الضغوط بشكل فعال؟", en: "Do you handle stress effectively?" },
            { ar: "هل تستطيع التكيف مع التغيرات؟", en: "Can you adapt to changes?" },
            { ar: "هل تتحمل المسؤولية عن مشاعرك؟", en: "Do you take responsibility for your emotions?" }
        ],
        options: [
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'ذكاء عاطفي مرتفع', desc: 'تمتلك مهارات عالية في الذكاء العاطفي.' },
                    { level: 'ذكاء عاطفي متوسط', desc: 'مهارات الذكاء العاطفي جيدة ولكن يمكن تطويرها.' },
                    { level: 'ذكاء عاطفي يحتاج تطوير', desc: 'هناك حاجة لتطوير مهارات الذكاء العاطفي.' }
                ],
                en: [
                    { level: 'High Emotional Intelligence', desc: 'You have high emotional intelligence skills.' },
                    { level: 'Moderate Emotional Intelligence', desc: 'Emotional intelligence skills are good but can be developed.' },
                    { level: 'Emotional Intelligence Needs Development', desc: 'There is a need to develop emotional intelligence skills.' }
                ]
            };
            
            if (score >= 30) return results[lang][0];
            if (score >= 20) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس العنف الأسري ==========
    {
        id: 'domestic-violence',
        title: {ar:'مقياس العنف الأسري',en:'Domestic Violenc  Scale'},
        description: { 
            ar: 'تقييم التعرض للعنف الأسري وتأثيره على الصحة النفسية',
            en: 'Assessment of exposure to domestic violence and its impact on mental health'
        },
        category: 'women',
        icon: 'fas fa-shield-alt',
        questions: 8,
        time: '5-7',
        rating: 4,
        reviews: 52,
        questions: [
            { ar: "هل تعرضت للإهانة أو السب من قبل أحد أفراد أسرتك؟", en: "Have you been insulted or cursed by a family member?" },
            { ar: "هل تعرضت للتهديد أو التخويف؟", en: "Have you been threatened or intimidated?" },
            { ar: "هل تعرضت للعزل عن الأهل والأصدقاء؟", en: "Have you been isolated from family and friends?" },
            { ar: "هل تعرضت للحرمان من الموارد المالية؟", en: "Have you been deprived of financial resources?" },
            { ar: "هل تعرضت للإيذاء الجسدي؟", en: "Have you been physically abused?" },
            { ar: "هل تعرضت للإكراه على ممارسة العلاقة الزوجية؟", en: "Have you been coerced into marital relations?" },
            { ar: "هل تعرضت للتحكم في قراراتك الشخصية؟", en: "Have you been controlled in your personal decisions?" },
            { ar: "هل تعرضت للمضايقات أو التحرش؟", en: "Have you been harassed or molested?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'لا يوجد عنف أسري', desc: 'لم تتعرض للعنف الأسري. هذا مؤشر جيد على بيئة أسرية آمنة.' },
                    { level: 'عنف أسري بسيط', desc: 'تعرضت لبعض أشكال العنف الأسري البسيطة. قد تحتاج لدعم.' },
                    { level: 'عنف أسري متوسط', desc: 'تعرضت لمستوى متوسط من العنف الأسري. ننصح بطلب المساعدة المتخصصة.' },
                    { level: 'عنف أسري شديد', desc: 'تعرضت لمستوى شديد من العنف الأسري. ننصح بطلب المساعدة الفورية.' }
                ],
                en: [
                    { level: 'No Domestic Violence', desc: 'You have not been exposed to domestic violence. This is a good indicator of a safe family environment.' },
                    { level: 'Mild Domestic Violence', desc: 'You have been exposed to some forms of mild domestic violence. You may need support.' },
                    { level: 'Moderate Domestic Violence', desc: 'You have been exposed to a moderate level of domestic violence. We recommend seeking specialized help.' },
                    { level: 'Severe Domestic Violence', desc: 'You have been exposed to a severe level of domestic violence. We recommend seeking immediate help.' }
                ]
            };
            
            if (score <= 8) return results[lang][0];
            if (score <= 16) return results[lang][1];
            if (score <= 24) return results[lang][2];
            return results[lang][3];
        }
    },
    
    // ========== مقياس الصدمات النفسية ==========
    {
        id: 'trauma',
        title: { ar: 'مقياس الصدمات النفسية', en: 'Psychological Trauma Scale' },
        description: { 
            ar: 'تقييم التعرض للصدمات النفسية وتأثيرها على الحالة النفسية',
            en: 'Assessment of exposure to psychological trauma and its impact on mental state'
        },
        category: 'women',
        icon: 'fas fa-first-aid',
        questions: 8,
        time: '6-8',
        rating: 4,
        reviews: 67,
        questions: [
            { ar: "هل تعرضت لحدث صادم في الماضي؟", en: "Have you experienced a traumatic event in the past?" },
            { ar: "هل تعاني من ذكريات متكررة عن الحدث؟", en: "Do you suffer from recurring memories of the event?" },
            { ar: "هل تعاني من كوابيس متعلقة بالحدث؟", en: "Do you have nightmares related to the event?" },
            { ar: "هل تشعر بالضيق عند تذكر الحدث؟", en: "Do you feel distressed when remembering the event?" },
            { ar: "هل تتجنب التفكير في الحدث؟", en: "Do you avoid thinking about the event?" },
            { ar: "هل فقدت الاهتمام بالأنشطة التي كنت تستمتع بها؟", en: "Have you lost interest in activities you used to enjoy?" },
            { ar: "هل تشعر بالانعزال عن الآخرين؟", en: "Do you feel isolated from others?" },
            { ar: "هل تواجه صعوبة في النوم أو التركيز؟", en: "Do you have difficulty sleeping or concentrating?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'تأثير طفيف', desc: 'التعرض للصدمة لم يؤثر بشكل كبير على حياتك.' },
                    { level: 'تأثير متوسط', desc: 'هناك تأثير واضح للصدمة على حياتك. قد تحتاج للدعم.' },
                    { level: 'تأثير شديد', desc: 'التعرض للصدمة أثر بشكل كبير على حياتك. ننصح بمراجعة أخصائي.' }
                ],
                en: [
                    { level: 'Mild Impact', desc: 'Exposure to trauma has not significantly affected your life.' },
                    { level: 'Moderate Impact', desc: 'There is a clear impact of trauma on your life. You may need support.' },
                    { level: 'Severe Impact', desc: 'Exposure to trauma has significantly affected your life. We recommend seeing a specialist.' }
                ]
            };
            
            if (score <= 8) return results[lang][0];
            if (score <= 16) return results[lang][1];
            return results[lang][2];
        }
    },

    // ========== مقياس التواصل الأسري ==========
    {
        id: 'family-communication',
        title: { ar: 'مقياس التواصل الأسري', en: 'Family Communication Scale' },
        description: { 
            ar: 'تقييم جودة التواصل والتفاعلات within العائلة',
            en: 'Assessment of communication quality and interactions within the family'
        },
        category: 'women',
        icon: 'fas fa-home-heart',
        questions: 8,
        time: '5-7',
        rating: 4,
        reviews: 78,
        questions: [
            { ar: "هل تتحدث مع أفراد عائلتك عن مشاعرك بحرية؟", en: "Do you freely talk about your feelings with family members?" },
            { ar: "هل يستمع أفراد العائلة لبعضهم البعض باهتمام؟", en: "Do family members listen to each other attentively?" },
            { ar: "هل تحل المشكلات العائلية بالحوار والتفاهم؟", en: "Are family problems solved through dialogue and understanding?" },
            { ar: "هل تشعر بالراحة في التعبير عن آرائك في المنزل؟", en: "Do you feel comfortable expressing your opinions at home?" },
            { ar: "هل يظهر أفراد العائلة الاحترام والتقدير لبعضهم؟", en: "Do family members show respect and appreciation for each other?" },
            { ar: "هل تشارك العائلة في أنشطة ووقت ممتع معاً؟", en: "Does the family share activities and enjoyable time together?" },
            { ar: "هل تدعم العائلة بعضها في الأوقات الصعبة؟", en: "Does the family support each other during difficult times?" },
            { ar: "هل تشعر أن التواصل في عائلتك إيجابي وفعال؟", en: "Do you feel that communication in your family is positive and effective?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'تواصل ممتاز', desc: 'جودة التواصل الأسري ممتازة. استمروا في هذا النمط الإيجابي.' },
                    { level: 'تواصل جيد', desc: 'التواصل الأسري جيد ولكن هناك مجالات للتحسين.' },
                    { level: 'تواصل يحتاج تحسين', desc: 'هناك حاجة لتحسين مهارات التواصل within العائلة.' }
                ],
                en: [
                    { level: 'Excellent Communication', desc: 'Family communication quality is excellent. Continue this positive pattern.' },
                    { level: 'Good Communication', desc: 'Family communication is good but there are areas for improvement.' },
                    { level: 'Communication Needs Improvement', desc: 'There is a need to improve communication skills within the family.' }
                ]
            };
            
            if (score >= 25) return results[lang][0];
            if (score >= 15) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس ضغوط تربية الأطفال ==========
    {
        id: 'parenting-stress',
        title: { ar: 'مقياس ضغوط تربية الأطفال', en: 'Parenting Stress Scale' },
        description: { 
            ar: 'تقييم مستوى الضغوط والتحديات في تربية الأطفال',
            en: 'Assessment of stress levels and challenges in parenting'
        },
        category: 'women',
        icon: 'fas fa-baby',
        questions: 10,
        time: '6-8',
        rating: 4,
        reviews: 89,
        questions: [
            { ar: "هل تشعر بالإرهاق من متطلبات تربية الأطفال؟", en: "Do you feel overwhelmed by parenting demands?" },
            { ar: "هل تواجه صعوبة في تحقيق التوازن between العمل والعائلة؟", en: "Do you have difficulty balancing work and family?" },
            { ar: "هل تشعر بالقلق المستمر regarding مستقبل أطفالك؟", en: "Do you feel constant anxiety about your children future?" },
            { ar: "هل تواجه تحديات في التعامل with السلوكيات الصعبة للأطفال؟", en: "Do you face challenges dealing with children difficult behaviors?" },
            { ar: "هل تشعر بعدم الكفاءة في دورك كوالد/ة؟", en: "Do you feel incompetent in your role as a parent?" },
            { ar: "هل يؤثر التوتر في التربية on صحتك النفسية؟", en: "Does parenting stress affect your mental health?" },
            { ar: "هل تجد صعوبة في تخصيص وقت لنفسك؟", en: "Do you find it difficult to allocate time for yourself?" },
            { ar: "هل تشعر بالعزلة في رحلة التربية؟", en: "Do you feel isolated in your parenting journey?" },
            { ar: "هل تواجه صعوبات مالية تؤثر on التربية؟", en: "Do you face financial difficulties that affect parenting?" },
            { ar: "هل تشعر أنك تحصل on الدعم الكافي في التربية؟", en: "Do you feel you receive adequate support in parenting?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'ضغوط منخفضة', desc: 'مستوى الضغوط في التربية منخفض ومقبول.' },
                    { level: 'ضغوط متوسطة', desc: 'هناك ضغوط متوسطة في التربية تحتاج للاهتمام.' },
                    { level: 'ضغوط عالية', desc: 'مستوى الضغوط في التربية مرتفع ويحتاج لدعم فوري.' }
                ],
                en: [
                    { level: 'Low Stress', desc: 'Parenting stress level is low and acceptable.' },
                    { level: 'Moderate Stress', desc: 'There are moderate parenting stresses that need attention.' },
                    { level: 'High Stress', desc: 'Parenting stress level is high and requires immediate support.' }
                ]
            };
            
            if (score <= 15) return results[lang][0];
            if (score <= 30) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس الدافعية الدراسية ==========
    {
        id: 'academic-motivation',
        title: { ar: 'مقياس الدافعية الدراسية', en: 'Academic Motivation Scale' },
        description: { 
            ar: 'تقييم مستوى الحافز والرغبة في التعلم والتحصيل الدراسي',
            en: 'Assessment of motivation level and desire for learning and academic achievement'
        },
        category: 'children',
        icon: 'fas fa-graduation-cap',
        questions: 8,
        time: '4-6',
        rating: 4,
        reviews: 72,
        questions: [
            { ar: "هل تشعر بالحماس للذهاب إلى المدرسة؟", en: "Do you feel enthusiastic about going to school?" },
            { ar: "هل تستمتع بتعلم مواضيع جديدة؟", en: "Do you enjoy learning new subjects?" },
            { ar: "هل تبذل جهداً إضافياً في واجباتك المدرسية؟", en: "Do you put extra effort into your school assignments?" },
            { ar: "هل تحب المشاركة في الأنشطة الصفية؟", en: "Do you like participating in classroom activities?" },
            { ar: "هل تضع أهدافاً دراسية لنفسك؟", en: "Do you set academic goals for yourself?" },
            { ar: "هل تشعر بالفخر when تحقق نتائج جيدة؟", en: "Do you feel proud when you achieve good results?" },
            { ar: "هل تبحث عن معلومات إضافية outside المنهج الدراسي؟", en: "Do you seek additional information outside the curriculum?" },
            { ar: "هل تحلم بمستقبل أكاديمي ومهني ناجح؟", en: "Do you dream of a successful academic and professional future?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'دافعية عالية', desc: 'مستوى الدافعية الدراسية ممتاز. استمر في هذا الحماس.' },
                    { level: 'دافعية متوسطة', desc: 'الدافعية الدراسية جيدة ولكن يمكن تعزيزها أكثر.' },
                    { level: 'دافعية منخفضة', desc: 'هناك حاجة لتعزيز الدافعية towards الدراسة والتعلم.' }
                ],
                en: [
                    { level: 'High Motivation', desc: 'Academic motivation level is excellent. Continue this enthusiasm.' },
                    { level: 'Moderate Motivation', desc: 'Academic motivation is good but can be further enhanced.' },
                    { level: 'Low Motivation', desc: 'There is a need to enhance motivation towards studying and learning.' }
                ]
            };
            
            if (score >= 25) return results[lang][0];
            if (score >= 15) return results[lang][1];
            return results[lang][2];
        }
    },    
    
    // ========== مقياس الضغوط المهنية ==========
    {
        id: 'work-stress',
        title: { ar: 'مقياس الضغوط المهنية', en: 'Professional Stress Scale' },
        description: { 
            ar: 'تقييم مستوى الضغوط والتحديات في بيئة العمل',
            en: 'Assessment of stress levels and challenges in the work environment'
        },
        category: 'women',
        icon: 'fas fa-briefcase',
        questions: 10,
        time: '6-8',
        rating: 4,
        reviews: 94,
        questions: [
            { ar: "هل تشعر بضغط كبير due to عبء العمل؟", en: "Do you feel great pressure due to workload?" },
            { ar: "هل تواجه صعوبة في تحقيق التوازن between العمل والحياة الشخصية؟", en: "Do you have difficulty balancing work and personal life?" },
            { ar: "هل تشعر بعدم الأمان الوظيفي؟", en: "Do you feel job insecurity?" },
            { ar: "هل تواجه تحديات في العلاقات with زملاء العمل؟", en: "Do you face challenges in relationships with colleagues?" },
            { ar: "هل تشعر بعدم التقدير في عملك؟", en: "Do you feel unappreciated at work?" },
            { ar: "هل تؤثر ضغوط العمل on صحتك النفسية؟", en: "Does work stress affect your mental health?" },
            { ar: "هل تواجه صعوبة في تلبية توقعات الرؤساء؟", en: "Do you have difficulty meeting supervisors expectations?" },
            { ar: "هل تشعر بالإرهاق due to ساعات العمل الطويلة؟", en: "Do you feel exhausted due to long working hours?" },
            { ar: "هل تواجه صعوبات في التكيف with التغيرات التنظيمية؟", en: "Do you have difficulties adapting to organizational changes?" },
            { ar: "هل تشعر أنك تحصل on دعم كافي في العمل؟", en: "Do you feel you receive adequate support at work?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'ضغوط مهنية منخفضة', desc: 'مستوى الضغوط المهنية مقبول ومنخفض.' },
                    { level: 'ضغوط مهنية متوسطة', desc: 'هناك ضغوط مهنية متوسطة تحتاج للتعامل معها.' },
                    { level: 'ضغوط مهنية عالية', desc: 'مستوى الضغوط المهنية مرتفع ويحتاج لتدخل.' }
                ],
                en: [
                    { level: 'Low Professional Stress', desc: 'Professional stress level is acceptable and low.' },
                    { level: 'Moderate Professional Stress', desc: 'There are moderate professional stresses that need to be addressed.' },
                    { level: 'High Professional Stress', desc: 'Professional stress level is high and requires intervention.' }
                ]
            };
            
            if (score <= 15) return results[lang][0];
            if (score <= 30) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس مهارات التكيف ==========
    {
        id: 'coping-skills',
        title: { ar: 'مقياس مهارات التكيف', en: 'Coping Skills Scale' },
        description: { 
            ar: 'تقييم استراتيجيات التعامل مع الضغوط والتحديات',
            en: 'Assessment of strategies for dealing with stresses and challenges'
        },
        category: 'women',
        icon: 'fas fa-shield-virus',
        questions: 8,
        time: '5-7',
        rating: 4,
        reviews: 81,
        questions: [
            { ar: "هل تبحث عن الدعم الاجتماعي when تواجه مشكلات؟", en: "Do you seek social support when facing problems?" },
            { ar: "هل تستخدم تقنيات الاسترخاء للتعامل with التوتر؟", en: "Do you use relaxation techniques to deal with stress?" },
            { ar: "هل تحلل المشكلات بهدوء before اتخاذ القرارات؟", en: "Do you analyze problems calmly before making decisions?" },
            { ar: "هل تمارس الرياضة أو الأنشطة البدنية للتعامل with الضغوط؟", en: "Do you exercise or engage in physical activities to deal with stress?" },
            { ar: "هل تستطيع التعبير عن مشاعرك بطريقة صحية؟", en: "Can you express your feelings in a healthy way?" },
            { ar: "هل تضع أهدافاً واقعية يمكن تحقيقها؟", en: "Do you set realistic achievable goals?" },
            { ar: "هل تستطيع إدارة وقتك بشكل فعال؟", en: "Can you manage your time effectively?" },
            { ar: "هل تبحث عن حلول إبداعية للمشكلات؟", en: "Do you seek creative solutions to problems?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'مهارات تكيف ممتازة', desc: 'تمتلك استراتيجيات فعالة للتعامل مع الضغوط.' },
                    { level: 'مهارات تكيف جيدة', desc: 'مهارات التكيف جيدة ولكن يمكن تطويرها أكثر.' },
                    { level: 'مهارات تكيف تحتاج تطوير', desc: 'هناك حاجة لتعلم استراتيجيات أفضل للتعامل مع الضغوط.' }
                ],
                en: [
                    { level: 'Excellent Coping Skills', desc: 'You have effective strategies for dealing with stress.' },
                    { level: 'Good Coping Skills', desc: 'Coping skills are good but can be further developed.' },
                    { level: 'Coping Skills Need Development', desc: 'There is a need to learn better strategies for dealing with stress.' }
                ]
            };
            
            if (score >= 25) return results[lang][0];
            if (score >= 15) return results[lang][1];
            return results[lang][2];
        }
    },
    
    // ========== مقياس تنظيم الانفعالات ==========
    {
        id: 'emotional-regulation',
        title: { ar: 'مقياس تنظيم الانفعالات', en: 'Emotional Regulation Scale' },
        description: { 
            ar: 'تقييم القدرة على إدارة المشاعر والتحكم في الانفعالات',
            en: 'Assessment of the ability to manage emotions and control impulses'
        },
        category: 'women',
        icon: 'fas fa-balance-scale',
        questions: 8,
        time: '5-7',
        rating: 4,
        reviews: 76,
        questions: [
            { ar: "هل تستطيع التحكم في غضبك في المواقف الصعبة؟", en: "Can you control your anger in difficult situations?" },
            { ar: "هل تأخذ وقتاً للتفكير before الرد في النقاشات الحادة؟", en: "Do you take time to think before responding in heated discussions?" },
            { ar: "هل تستطيع تهدئة نفسك when تشعر بالتوتر؟", en: "Can you calm yourself when you feel stressed?" },
            { ar: "هل تعبر عن مشاعرك بطريقة مناسبة؟", en: "Do you express your feelings in an appropriate way?" },
            { ar: "هل تتجنب اتخاذ قرارات مهمة when تكون منفعلاً؟", en: "Do you avoid making important decisions when emotional?" },
            { ar: "هل تستطيع تحويل المشاعر السلبية to طاقة إيجابية؟", en: "Can you transform negative emotions into positive energy?" },
            { ar: "هل تتعرف على مشاعرك مبكراً before أن تتفاقم؟", en: "Do you recognize your emotions early before they escalate?" },
            { ar: "هل تستخدم تقنيات للسيطرة on الانفعالات المفاجئة؟", en: "Do you use techniques to control sudden impulses?" }
        ],
        options: [
            { ar: "أبداً", en: "Never" },
            { ar: "نادراً", en: "Rarely" },
            { ar: "أحياناً", en: "Sometimes" },
            { ar: "غالباً", en: "Often" },
            { ar: "دائماً", en: "Always" }
        ],
        scores: [0, 1, 2, 3, 4],
        interpretation: (score, lang) => {
            const results = {
                ar: [
                    { level: 'تنظيم انفعالي ممتاز', desc: 'قدرتك على إدارة المشاعر والانفعالات ممتازة.' },
                    { level: 'تنظيم انفعالي جيد', desc: 'قدرتك على تنظيم الانفعالات جيدة ولكن يمكن تحسينها.' },
                    { level: 'تنظيم انفعالي يحتاج تحسين', desc: 'هناك حاجة لتحسين مهارات إدارة المشاعر والانفعالات.' }
                ],
                en: [
                    { level: 'Excellent Emotional Regulation', desc: 'Your ability to manage emotions and impulses is excellent.' },
                    { level: 'Good Emotional Regulation', desc: 'Your emotional regulation ability is good but can be improved.' },
                    { level: 'Emotional Regulation Needs Improvement', desc: 'There is a need to improve emotion and impulse management skills.' }
                ]
            };
            
            if (score >= 25) return results[lang][0];
            if (score >= 15) return results[lang][1];
            return results[lang][2];
        }
    },

    
];
// تصنيفات المقاييس

export const resourcesData = [
    {
        id: 1,
        title: {
            ar: 'فهم القلق وكيفية التعامل معه',
            en: 'Understanding Anxiety and How to Deal with It'
        },
        excerpt: {
            ar: 'مقال شامل عن أسباب القلق وأعراضه واستراتيجيات فعالة للتعامل معه.',
            en: 'Comprehensive article about anxiety causes, symptoms, and effective coping strategies.'
        },
        image: 'https://picsum.photos/seed/anxiety-article/400/200.jpg',
        link: '#'
    },
    {
        id: 2,
        title: {
            ar: 'تقنيات الاسترخاء العميق',
            en: 'Deep Relaxation Techniques'
        },
        excerpt: {
            ar: 'تعلم تقنيات الاسترخاء المختلفة مثل التنفس العميق والتأمل واليوغا.',
            en: 'Learn various relaxation techniques such as deep breathing, meditation, and yoga.'
        },
        image: 'https://picsum.photos/seed/relaxation-techniques/400/200.jpg',
        link: '#'
    },
    {
        id: 3,
        title: {
            ar: 'بناء الثقة بالنفس',
            en: 'Building Self-Confidence'
        },
        excerpt: {
            ar: 'دليل عملي لزيادة الثقة بالنفس وتقدير الذات من خلال تمارين بسيطة.',
            en: 'Practical guide to increase self-confidence and self-esteem through simple exercises.'
        },
        image: 'https://picsum.photos/seed/self-confidence/400/200.jpg',
        link: '#'
    },
    {
        id: 4,
        title: {
            ar: 'إدارة الضغوط النفسية',
            en: 'Managing Psychological Stress'
        },
        excerpt: {
            ar: 'أساليب فعالة للتعامل مع الضغوط اليومية والحفاظ على الصحة النفسية.',
            en: 'Effective methods for dealing with daily stress and maintaining mental health.'
        },
        image: 'https://picsum.photos/seed/stress-management/400/200.jpg',
        link: '#'
    },
    {
        id: 5,
        title: {
            ar: 'تحسين جودة النوم',
            en: 'Improving Sleep Quality'
        },
        excerpt: {
            ar: 'نصائح وعادات لتحسين نمط النوم والحصول على راحة أفضل.',
            en: 'Tips and habits to improve sleep patterns and get better rest.'
        },
        image: 'https://picsum.photos/seed/sleep-quality/400/200.jpg',
        link: '#'
    },
    {
        id: 6,
        title: {
            ar: 'التواصل الفعال في العلاقات',
            en: 'Effective Communication in Relationships'
        },
        excerpt: {
            ar: 'مهارات التواصل التي تعزز العلاقات الشخصية والمهنية.',
            en: 'Communication skills that enhance personal and professional relationships.'
        },
        image: 'https://picsum.photos/seed/communication/400/200.jpg',
        link: '#'
    }
];

export const menuItemsData = [
    { id: 'home', link: '#' },
    { id: 'measures', link: '#' },
    { id: 'sessions', link: '#' },
    { id: 'library', link: '#' },
    { id: 'courses', link: '#' },
    { id: 'contact', link: '#' }
];

export const filtersData = [
    { id: 'allMeasures' },
    { id: 'forWomen' },
    { id: 'forChildren' },
];

export const quickLinksData = [
    { id: 'home', url: '#' },
    { id: 'measures', url: '#' },
    { id: 'sessions', url: '#' },
    { id: 'library', url: '#' },
    { id: 'aboutUs', url: '#' }
];

export const titles = {
    home: 'الرئيسية',
    measures: 'المقاييس',
    sessions: 'الجلسات',
    library: 'المكتبة',
    courses: 'الدورات',
    contact: 'اتصل بنا',
    aboutUs: 'من نحن',
    allMeasures: 'جميع المقاييس',
    forWomen: 'للمرأة',
    forChildren: 'للطفل',
    forSpecialists: 'للمختصين'
};



export const categoriesData = [
    {
        id: 'all',
        title: { ar: 'جميع المقاييس', en: 'All Measures' },
        description:{
            ar:'استعرض جميع المقاييس النفسية المتاحة في المنصة',
            en:"Browse all available psychological measures on the platform"
        },
        icon: 'fas fa-layer-group',
        filter: 'allMeasures',
        color: 'bg-gradient-to-l from-primary-green to-primary-pink'
    },
    {
        id: 'women',
        title: { ar: 'للمرأة', en: 'For Women' },
        description:{
            ar:'مقاييس متخصصة لصحة المرأة النفسية والتربوية',
            en:"Specialized measures for women's mental and educational health"
        },
        icon: 'fas fa-female',
        filter: 'forWomen',
        color: 'bg-gradient-to-l from-primary-green to-[#814251]'
    },
    {
        id: 'children',
        title: { ar: 'للأطفال', en: 'For Children' },
        description: { 
            ar: 'مقاييس مصممة خصيصاً للأطفال والمراهقين لتقييم النمو والمهارات',
            en: "Measures specifically designed for children and adolescents to assess development and skills"
        },
        icon: 'fas fa-child',
        filter: 'forChildren',
        color: 'bg-gradient-to-l from-primary-green to-[#954529]'
    }
];