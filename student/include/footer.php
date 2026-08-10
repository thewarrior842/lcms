<!-- Footer -->
<div class="footer">
    <p>ATTC Student Dashboard • Developed By: Deep Karmakar</p>
</div>
</div>
</div>
<script>
document.querySelectorAll(".subject-btn").forEach(btn=>{
    btn.addEventListener("click",function(){
        let month=this.dataset.month || "all";
        window.location.href="?month="+month;
    });
});
</script>


</body>

</html>