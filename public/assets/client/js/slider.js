window.addEventListener("load", function () {
    const heroWrapper = document.querySelector('.hero-carousel');
    if (!heroWrapper) return;

    const mainslider = heroWrapper.querySelector('.main-slider');
    const itemslider = heroWrapper.querySelectorAll('.wrapper-item-slider');

    if (itemslider.length === 0) return;

    let withItem = itemslider[0].offsetWidth;
    let lenghtItem = itemslider.length;
    let tranX = 0;
    let index = 1;
    let isHovered = false; // Biến theo dõi xem chuột có đang nằm trong slider không

    const back = heroWrapper.querySelector('.back-slider-card');
    const next = heroWrapper.querySelector('.next-slider-card');

    // 1. CÀI ĐẶT HIỆU ỨNG ẨN/HIỆN CHO CẢ 2 NÚT
    const setupButton = (btn) => {
        if (btn) {
            btn.style.opacity = '0';
            btn.style.visibility = 'hidden';
            btn.style.transition = 'all 0.3s ease';
        }
    };
    setupButton(back);
    setupButton(next);

    // 2. HÀM ĐIỀU KHIỂN NÚT THÔNG MINH
    const updateArrows = () => {
        // Nếu chuột không nằm trong slider -> Ẩn cả 2
        if (!isHovered) {
            if (back) { back.style.opacity = '0'; back.style.visibility = 'hidden'; }
            if (next) { next.style.opacity = '0'; next.style.visibility = 'hidden'; }
            return;
        }

        // Đang ở slide đầu tiên -> Chỉ hiện Next, ẩn Back
        if (index === 1) {
            if (back) { back.style.opacity = '0'; back.style.visibility = 'hidden'; }
            if (next) { next.style.opacity = '1'; next.style.visibility = 'visible'; }
        } 
        // Đang ở slide cuối cùng -> Chỉ hiện Back, ẩn Next
        else if (index === lenghtItem) {
            if (back) { back.style.opacity = '1'; back.style.visibility = 'visible'; }
            if (next) { next.style.opacity = '0'; next.style.visibility = 'hidden'; }
        } 
        // Nằm ở các slide giữa -> Hiện cả 2
        else {
            if (back) { back.style.opacity = '1'; back.style.visibility = 'visible'; }
            if (next) { next.style.opacity = '1'; next.style.visibility = 'visible'; }
        }
    };

    // 3. BẮT SỰ KIỆN RÊ CHUỘT (HOVER)
    heroWrapper.addEventListener('mouseenter', () => {
        isHovered = true;
        updateArrows();
    });

    heroWrapper.addEventListener('mouseleave', () => {
        isHovered = false;
        updateArrows();
    });

    window.addEventListener('resize', () => {
        withItem = itemslider[0].offsetWidth;
        tranX = -(index - 1) * withItem;
        mainslider.style.transform = `translateX(${tranX}px)`;
    });

    // 4. HÀM CHUYỂN SLIDE TỚI
    const changeNext = function () {
        if (index >= lenghtItem) {
            // Tự động đẩy về đầu khi đang ở slide cuối (Dành cho Auto Slide)
            index = 1;
            tranX = 0;
            mainslider.style.transition = "transform 0.8s cubic-bezier(0.25, 1, 0.5, 1)";
        } else {
            // Trượt bình thường
            index++;
            tranX = -(index - 1) * withItem;
            mainslider.style.transition = "transform 0.5s ease-in-out";
        }
        mainslider.style.transform = `translateX(${tranX}px)`;
        updateArrows(); // Xét lại việc hiển thị nút sau khi chuyển
    }

    // 5. HÀM CHUYỂN SLIDE LÙI
    const changeback = function () {
        if (index <= 1) return; // Không cho lùi nếu đang ở slide đầu
        index--;
        tranX = -(index - 1) * withItem;
        mainslider.style.transition = "transform 0.5s ease-in-out";
        mainslider.style.transform = `translateX(${tranX}px)`;
        updateArrows(); // Xét lại việc hiển thị nút sau khi chuyển
    }

    let autoSlide = setInterval(changeNext, 4000);

    const resetInterval = () => {
        clearInterval(autoSlide);
        autoSlide = setInterval(changeNext, 4000);
    };

    if (next) {
        next.onclick = function() {
            if (index < lenghtItem) { // Chỉ cho bấm nếu chưa tới cuối
                changeNext();
                resetInterval();
            }
        };
    }

    if (back) {
        back.onclick = function() {
            if (index > 1) { // Chỉ cho bấm nếu không ở đầu
                changeback();
                resetInterval();
            }
        };
    }
});