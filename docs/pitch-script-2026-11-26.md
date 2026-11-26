# AgroSkan — 4 daqiqalik jonli ko'rsatish skripti (oraliq baholash, 26-noyabr)

Vaqt: 4 daqiqa gapirish + 2 daqiqa savol-javob. Bitta kishi gapiradi, qolganlar savollarga javob beradi.
Slayd kerak emas — hammasi noutbukda jonli ko'rsatiladi.

---

## 0:00–0:35 — Muammo (1-2 gap, lekin his qildirib ayting)

> "Issiqxonada kasallik yoki zararkunanda paydo bo'lganda, fermer buni ko'pincha 3-4 kundan keyin — hosil allaqachon zararlanganda bilib qoladi. Sababi oddiy: bir issiqxonada minglab tup bor, hammasini har kuni qo'lda ko'zdan kechirish imkonsiz. Natijada hosilning sezilarli qismi yo'qoladi."

## 0:35–1:00 — Kimda bu muammo bor

> "Bu muammo O'zbekistondagi har qanday issiqxona fermeri va agrokompaniyada bor — kichik oilaviy issiqxonadan tortib katta agroholdinglargacha. Biz Jizzax va Farg'ona hududidagi real issiqxona ma'lumotlari bilan ishladik."

## 1:00–1:40 — Yechim va AI roli

> "Yechimimiz — AgroSkan. Issiqxonaning har bir qatorida relslar bo'ylab yuradigan kichik robot bor. Robot kamerasi 360 gradusga aylanib, har bir tupni suratga oladi. Har bir rasm darhol sun'iy intellekt orqali tahlil qilinadi — bu yerda AI aniq ishni bajaradi: rasmga qarab o'simlik sog'lom, kasal, zararkunanda bosgan yoki oziq moddasi yetishmayaptimi — shuni aniqlaydi. Muhim nuqta: robot 24/7 ishlamaydi, faqat fermer tugma bosganda ishga tushadi — bu energiya va resursni tejaydi."

## 1:40–3:10 — Jonli ko'rsatish (eng muhim qism — amalda ko'rsating, gapirmang faqat)

Ketma-ketlik (oldindan noutbukda ochib qo'ying, internet bo'lmasa — oldindan yozilgan video ishlatiladi):

1. **Dashboard'ni ochib ko'rsatish** — "Mana, bu bizning boshqaruv panelimiz, production serverda jonli ishlayapti." Sog'liq indeksi, faol ogohlantirishlar, hosil prognozini ko'rsating.
2. **"Tahlil qilish" tugmasini bosish** — "Men hozir shu tugmani bosaman — va barcha qatorlardagi robotlar bir vaqtda ishga tushadi." Robot holatlari "skanerlamoqda"ga o'tganini ko'rsating.
3. **Natijani kutish paytida** — "Bir necha soniyada robotlar o'z qatorini aylanib chiqadi, har bir tupni qayta tekshiradi." Robotlar "tayyor"ga qaytganda yangi ogohlantirish/holat o'zgarishini ko'rsating.
4. **AI Tahlil sahifasini ochish** — bitta rasm yuklab (oldindan tanlab qo'ying), "Mana, bu — haqiqiy o'qitilgan model, oldindan tayyorlangan javob emas." Natija va ishonch foizini ko'rsating (pomidorda ~95% aniqlik).
5. **Telegram botni ochish** (telefonda, agar vaqt qolsa) — `/holat` buyrug'ini yuborib, botning javobini ko'rsating.

> "Bularning barchasi — dashboard, AI tahlil, Telegram bot, PDF hisobot — hozir production serverda, haqiqiy ma'lumotlar bilan ishlayapti. Bu demo emas, ishlaydigan mahsulot."

## 3:10–3:45 — Finalgacha nima qo'shamiz

> "Hozir ikki ekin turi bilan ishlaymiz — pomidorda aniqlik 95 foizga yaqin, qulupnayda esa 70 foiz, va biz buning sababini topdik: zararkunanda sinfi uchun juda kam o'qitish rasmi bor edi, buni hal qilish ustida ishlaymiz. Finalgacha: haqiqiy robot bilan integratsiya, qulupnay modelini yaxshilash, va yana ekin turlarini qo'shish rejamiz bor."

## 3:45–4:00 — Yakunlovchi jumla

> "Xulosa qilib aytsam: biz fermerga 'robot ko'zi' beramiz — u ko'rmagan narsani AI ko'radi, va bu hosilni yo'qotishdan oldin ogohlantiradi."

---

## Savol-javobga tayyorgarlik (mentorlar odatda shu haqida so'raydi)

**"Model qancha aniq ishlaydi, qayerdan tekshirdingiz?"**
> Pomidor modelini haqiqiy rasmlar bilan sinab ko'rdik — 95%+ aniqlik, to'g'ri tashxis qo'ydi (masalan Septoriya dog'lanishini 96% ishonch bilan topdi). Qulupnayda past (~70%) — buning sababini ham aniqladik: zararkunanda klassi uchun atigi 16 ta noyob o'qitish rasmi bor edi.

**"Robot hali yo'q, demo qanday ishlayapti?"**
> Robot mexanik qismi hali qurilmagan, lekin butun dastur arxitekturasi (tugma → signal → skanerlash → natija → dashboard yangilanishi) to'liq ishlaydi — hozir buni real fon-jarayon (queue worker) simulyatsiya qiladi. Robot tayyor bo'lganda faqat bitta qismni — kamera signalini — ulaymiz, qolgan hammasi tayyor.

**"Fermer buni qanday ishlatadi, qiyin emasmi?"**
> Yo'q — fermerga faqat bitta tugma kerak: "Tahlil qilish". Qolgan hamma narsani Telegram botdan ham kuzatish mumkin — qo'shimcha ilova o'rnatish shart emas.

**"Nega 24/7 ishlamaydi?"**
> Chunki doimiy ishlash elektr energiyasi va mexanik resursni behuda sarflaydi — fermerga kun davomida bir-ikki marta tahlil kifoya, shuning uchun talab bo'yicha ishlashni tanladik.

**"Daromad modeli qanday?"**
> Oylik obuna (issiqxona maydoniga qarab) + robot jihozini sotish/ijaraga berish. Kasallikni kech aniqlash hosilning 20-30% gacha yo'qotishiga olib kelishi mumkin — bizning xizmat narxi shundan ancha arzon.

---

## Eslatma jamoaga

- Bitta kishi gapiradi, lekin savol kelganda tegishli qism ustida ishlagan kishi javob bersin (masalan AI savoliga — modelni sinagan kishi).
- Vaqtni nazorat qiling — 4 daqiqadan oshmasin, mentorlar vaqtga rioya qilishni ham baholaydi.
- Noutbuk to'liq zaryadda, internet yo'q bo'lsa ishlatiladigan qisqa video oldindan tayyor turishi kerak.
