</div>
</xai:function_call name="edit_file">
<parameter name="path">public/koleksi.php
</div>

<script>
    // Basic interactivity
    document.querySelectorAll('.game-card').forEach(card => {
        card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-5px)');
        card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
    });

    // Edit Modal JS (koleksi.php)
    const editModal = document.getElementById('editModal');
    const editBtns = document.querySelectorAll('.edit-btn');
    const closeBtn = document.querySelector('.close');
    const editForm = document.getElementById('editForm');

    editBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const platform = btn.dataset.platform;
            const progress = btn.dataset.progress;
            
            document.getElementById('editKoleksiId').value = id;
            editForm.platform.value = platform;
            editForm.progress.value = progress;
            editModal.style.display = 'flex';
        });
    });

    closeBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === editModal) editModal.style.display = 'none';
    });

    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(editForm);
        
        try {
            // Upload image first if present (separate request)
            let imagePath = null;
            if (editForm.image.files[0]) {
                const imgFormData = new FormData();
                imgFormData.append('koleksi_id', document.getElementById('editKoleksiId').value);
                imgFormData.append('image', editForm.image.files[0]);
                
                const imgResponse = await fetch('upload_image.php', {
                    method: 'POST',
                    body: imgFormData
                });
                const imgResult = await imgResponse.json();
                if (imgResult.success) imagePath = imgResult.image;
            }
            
            // Update progress/platform
            const progressFormData = new FormData();
            progressFormData.append('koleksi_id', document.getElementById('editKoleksiId').value);
            progressFormData.append('platform', editForm.platform.value);
            progressFormData.append('progress', editForm.progress.value);
            
            const progressResponse = await fetch('update_progress.php', {
                method: 'POST',
                body: progressFormData
            });
            
            if (progressResponse.ok) {
                editModal.style.display = 'none';
                location.reload();
            } else {
                alert('Error saving');
            }
        } catch (error) {
            alert('Network error: ' + error);
        }
    });

    // Delete functionality
    document.getElementById('deleteBtn').addEventListener('click', async () => {
        if (confirm('Hapus game dari library?')) {
            const koleksiId = document.getElementById('editKoleksiId').value;
            const response = await fetch('delete_koleksi.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'koleksi_id=' + koleksiId
            });
            
            if (response.ok) {
                location.reload();
            } else {
                alert('Delete failed');
            }
        }
    });
</script>
</body>
</html>

