window.addEventListener("load", function () {
    // 1. Chỉ tìm slider nằm trong khối card khuyến mãi
    const cardSection = document.querySelector('.slider-card');
    if (!cardSection) return;

    // Bắt đúng class theo HTML của bạn
    const sliderMain = cardSection.querySelector('.slider-main');
    const cardslider = cardSection.querySelectorAll('.card-slider');

    if (cardslider.length === 0) return;

    // FIX BUG: Thêm .offsetWidth để lấy chiều rộng (số), thay vì lấy thẻ HTML
    const widthCardSlider = cardslider[0].offsetWidth; 
    const lenghtCardSlider = cardslider.length;
    
    let tranX = 0;
    let index = 0;

    const backslider = cardSection.querySelector('.back-slider-card');
    const nextslider = cardSection.querySelector('.next-slider-card');

    const changenextslider = function () {
        if (index >= lenghtCardSlider - 1) {
            index = 0;
            tranX = 0;
            sliderMain.style.transform = `translateX(0px)`;
            return;
        }
        tranX = tranX - widthCardSlider;
        sliderMain.style.transform = `translateX(${tranX}px)`;
        index++;
    }

    const changebackslider = function () {
        if (index <= 0) {
            index = 0;
            return;
        }
        tranX = tranX + widthCardSlider;
        sliderMain.style.transform = `translateX(${tranX}px)`;
        index--;
    }

    if (nextslider) nextslider.onclick = changenextslider;
    if (backslider) backslider.onclick = changebackslider;

    // Khối sản phẩm thì để 5 giây hãy trượt
    setInterval(changenextslider, 5000); 
});