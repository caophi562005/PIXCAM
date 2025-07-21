//dùng để canh pading
window.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector("header");
  const content = document.querySelector(".content");
  content.style.paddingTop = header.offsetHeight + "px";
});
//Dùng để cuộn ngang trong khám phá phong cách của bạn
document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".poster-carousel");
  const btnLeft = document.querySelector(".btn-left");
  const btnRight = document.querySelector(".btn-right");

  const scrollStep = 320;
  let autoScroll;
  let restartTimeout;

  // Lưu bản gốc
  const original = container.innerHTML;

  // Clone trước và sau
  container.innerHTML = original + original + original;

  // Scroll đến vị trí giữa (bắt đầu tại phần thật)
  const originalWidth = container.scrollWidth / 3;
  container.scrollLeft = originalWidth;

  function startAutoScroll() {
    // Scroll ngay lập tức
    container.scrollBy({ left: scrollStep, behavior: "smooth" });

    autoScroll = setInterval(() => {
      container.scrollBy({ left: scrollStep, behavior: "smooth" });

      setTimeout(() => {
        if (container.scrollLeft >= originalWidth * 2) {
          container.scrollTo({ left: originalWidth, behavior: "auto" });
        }
      }, 500);
    }, 3000);
  }

  function resetAutoScrollDelay() {
    clearInterval(autoScroll);
    clearTimeout(restartTimeout);
    restartTimeout = setTimeout(() => {
      startAutoScroll();
    }, 3000);
  }

  startAutoScroll();

  btnLeft.addEventListener("click", () => {
    container.scrollBy({ left: -scrollStep, behavior: "smooth" });
    resetAutoScrollDelay();

    // Nếu cuộn về đầu clone → nhảy lại giữa
    setTimeout(() => {
      if (container.scrollLeft <= scrollStep) {
        container.scrollTo({ left: originalWidth, behavior: "auto" });
      }
    }, 500);
  });

  btnRight.addEventListener("click", () => {
    container.scrollBy({ left: scrollStep, behavior: "smooth" });
    resetAutoScrollDelay();

    setTimeout(() => {
      if (container.scrollLeft >= originalWidth * 2) {
        container.scrollTo({ left: originalWidth, behavior: "auto" });
      }
    }, 500);
  });
});

//nút click về đầu trang

const backToTopBtn = document.getElementById("backToTop");

window.addEventListener("scroll", function () {
  const nearBottom =
    window.innerHeight + window.scrollY >= document.body.offsetHeight - 100;

  if (nearBottom) {
    backToTopBtn.style.display = "flex";
  } else {
    backToTopBtn.style.display = "none";
  }
});

backToTopBtn.addEventListener("click", function () {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

// Đảm bảo khi load lại trang, nếu đang ở đầu thì ẩn nút
window.addEventListener("load", function () {
  if (window.scrollY === 0) {
    backToTopBtn.style.display = "none";
  }
});

const dropdown = document.getElementById("customDropdown");
const selectedText = document.getElementById("selectedText");
const sortInput = document.getElementById("sortInput");
const options = dropdown.querySelectorAll(".options li");

selectedText.addEventListener("click", () => {
  dropdown.classList.toggle("open");
});

options.forEach((option) => {
  option.addEventListener("click", () => {
    selectedText.textContent = option.textContent;
    sortInput.value = option.getAttribute("data-value");
    dropdown.classList.remove("open");
    document.getElementById("sortForm").submit();
  });
});

// Đóng dropdown khi click ra ngoài
document.addEventListener("click", (e) => {
  if (!dropdown.contains(e.target)) {
    dropdown.classList.remove("open");
  }
});
