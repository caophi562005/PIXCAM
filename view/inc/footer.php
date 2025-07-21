   <footer>
       <div class="logo_footer">
           <img src="/PIXCAM/view/img/home/logo.png" alt="" class="logo_footerIcon" />
       </div>
       <ul class="contact_footer">
           <li>
               <div class="item_title_contact">
                   <p class="title_contact">LIÊN HỆ</p>
               </div>
               <div class="content_contact">
                   <ul>
                       <li class="address_contact">
                           <i class="fa-solid fa-location-dot"></i> 02,Võ Oanh, Bình
                           Thạnh,TPHCM
                       </li>
                       <li class="address_contact">
                           <i class="fa-solid fa-phone"></i> Hotlline: 0336673831
                       </li>
                       <li class="address_contact">
                           <i class="fa-solid fa-envelope"></i> Email: pixcam@gmail.com
                       </li>
                   </ul>
               </div>
           </li>
           <li>
               <div class="item_title_contact">
                   <p class="title_contact">CHÍNH SÁCH</p>
               </div>
               <div class="content_contact">
                   <ul>
                       <li class="address_contact">
                           <a href="index.php?controller=CSTV&action=index
">Chính sách thành viên</a>
                       </li>
                       <li class="address_contact">
                           <a href="index.php?controller=CSDT&action=index
">Chính sách đổi trả</a>
                       </li>
                       <li class="address_contact">
                           <a href="index.php?controller=CSVC&action=index
">Chính sách vận chuyển</a>
                       </li>
                   </ul>
               </div>
           </li>
           <li>
               <div class="item_title_contact">
                   <p class="title_contact">ĐĂNG KÝ NHẬN TIN</p>
               </div>
               <div class="content_contact">
                   <ul>
                       <li class="address_contact">Nhận thông tin sản phẩm mới nhất</li>
                       <li class="address_contact">Thông tin sản phẩm khuyến mại</li>
                   </ul>
               </div>
           </li>
           <li>
               <div class="item_title_contact">
                   <p class="title_contact">KẾT NỐI</p>
               </div>
               <div class="content_contact">
                   <ul style="display: flex; gap: 20px">
                       <li class="address_contact">
                           <i class="fa-brands fa-facebook"></i>
                       </li>
                       <li class="address_contact">
                           <i class="fa-brands fa-instagram"></i>
                       </li>
                   </ul>
               </div>
           </li>
       </ul>
       <button id="backToTop" title="Lên đầu trang">
           <i class="fas fa-arrow-up"></i>
       </button>
   </footer>
   <script src="/PIXCAM/view/js/home.js"></script>
   <script>
function toggleAccountMenu() {
    const menu = document.getElementById("accountMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

window.addEventListener('click', function(e) {
    const menu = document.getElementById("accountMenu");
    const button = document.querySelector('.menu-button');
    if (!button.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = "none";
    }
});
   </script>
   </body>

   </html>