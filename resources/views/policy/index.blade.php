<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سياسة الخصوصية والشروط - عدسة سوما</title>

    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/studio-client/style.css">
    <link rel="stylesheet" href="/assets/css/studio-client/index.css">
    <link rel="stylesheet" href="/assets/css/studio-client/responsive.css">
    <link rel="stylesheet" href="/assets/css/customer/policy.css">
</head>
<body>
    @include('parts.navbar')

    <div class="policy-hero">
        <div class="container">
            <h1>سياسة الخصوصية والشروط</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item active" aria-current="page">سياسة الخصوصية</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="policy-container section-padding">
        <div class="container">
            <div class="policy-content glass-card">
                <section class="policy-section">
                    <h2>شروط الحجز والتصوير</h2>
                    <ul>
                        <li>التصوير في الاستوديو فقط ولا نوفر خدمات منزلية</li>
                        <li>يسمح بتصوير الرجال مع الأطفال خلال أول عشر دقائق من الجلسة فقط (بعدها يغادرون)</li>
                        <li>التصوير العائلي يتوفر في جميع الباكجات بمبلغ اضافي</li>
                        <li>يتم تصوير المواليد في ساعات الصباح فقط</li>
                        <li>لا يمكن طلب حذف الصور بعد التصوير</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>الخدمات والأسعار</h2>
                    <ul>
                        <li>يقوم الاستوديو بتوفير الخلفيات والاكسسوار والملابس للأطفال من الولادة لعمر السنة فقط</li>
                        <li>يتم زيادة 250 ريال في حالة اليوم الثاني و 350 لليوم الثالث</li>
                        <li>يتم تسليم الصور للزبون خلال شهرين كحد اقصى</li>
                        <li>ممكن اضافة عدد 2 صورة للأم والأب على البكج بقيمة 250 ريال</li>
                        <li>يتم زيادة 100 ريال لكل طفل في باكجات التصوير العائلي</li>
                        <li>يتم زيادة 200 ريال لإضافة تصوير اخ او اخت لمجموع 4 صور</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>سياسة الحجز والحضور</h2>
                    <ul>
                        <li>يرجى الحجز قبل الموعد بشهر كحد أدنى</li>
                        <li>أوقات العمل من السبت إلى الخميس، من الساعة 10:00 ص - 6:00 م</li>
                        <li>لا نعمل في العطلات الرسمية</li>
                        <li>دفع مقدم من قيمة الجلسة كعربون مسبقاً، وباقي المبلغ المتبقي عند انتهاء الجلسة</li>
                        <li>لا يتم ارجاع العربون في حالة الغاء الحجز، يسمح فقط بتأجيل الجلسة في مدة 6 شهر فقط (غير ذلك تكون الجلسة ملغية والعربون غير مسترجع)</li>
                        <li>يرجى التواجد قبل موعد الجلسة ب 10 دقائق، في حالة التأخر 20 دقيقه تعتبر الجلسة ملغية</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>سياسة الصور والتعديلات</h2>
                    <ul>
                        <li>عند السماح بعرض الصور تحصل على:
                            <ul>
                                <li>ترقية عدد الصور بعدد 3 صور للطفل في الفلاش</li>
                                <li>الحصول على صورة مطبوعة لوحة خشبية من اختيارك</li>
                            </ul>
                        </li>
                        <li>لعدم الاحراج عدم احضار اقرباء (فقط الام والاب والابناء)</li>
                        <li>في حال عدم سداد المبلغ المطلوب يتم تحويل الصور للأرشيف ويتم اخلاء المسؤولية من قبلنا</li>
                        <li>يتحمل الزبون قيمة التوصيل لاستلام الصور بحسب اسعار المندوبين</li>
                        <li>لا يتم تسليم الصور غير المعدلة للزبون ابداً منعاً للإحراج</li>
                        <li>في حال تم تعديل الصور المختارة من قبل الزبون و أراد الزبون تغييرها بعد التعديل، يتوجب دفع قيمتها</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>

    @include('parts.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
