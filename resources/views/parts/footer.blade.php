<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="logo-container">
                    <img src="/assets/images/logo.png" alt="عدسة سوما">
                </div>
                <p>نوثق لحظاتكم الجميلة بلمسة فنية مميزة</p>
            </div>
            <div class="col-md-4">
                <h3>تواصل معنا</h3>
                <p>
                    <i class="fas fa-phone"></i> +966561667885<br>
                    <i class="fas fa-envelope"></i> lens_soma@outlook.sa

                </p>
            </div>
            <div class="col-md-4">
                <h3>تابعنا على</h3>
                <div class="social-links">
                    <a href="/https://www.instagram.com/lens_soma_studio/?igsh=d2ZvaHZqM2VoMWsw#" class="me-2"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/966561667885" class="me-2"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; {{ date('Y') }} عدسة سوما. جميع الحقوق محفوظة</p>
        </div>
    </div>
</footer>

<!-- WhatsApp Fixed Button -->
<a href="https://wa.me/966561667885" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

<style>
.whatsapp-float {
    position: fixed;
    bottom: 40px;
    left: 40px;
    background-color: #25d366;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 2px 2px 3px #999;
    z-index: 100;
    transition: all 0.3s ease;
}

.whatsapp-float:hover {
    background-color: #128C7E;
    color: #FFF;
    transform: scale(1.1);
}

@media screen and (max-width: 767px) {
    .whatsapp-float {
        width: 50px;
        height: 50px;
        bottom: 20px;
        left: 20px;
        font-size: 25px;
    }
}
</style>
