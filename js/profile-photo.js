document.addEventListener('DOMContentLoaded', function() {
    const changePhotoBtn = document.getElementById('change_photo_btn');
    const fileInput = document.getElementById('foto_perfil');
    const uploadBtn = document.getElementById('upload_photo_btn');
    const profileImages = document.querySelectorAll('img[alt="Profile"]');
    
    if (!changePhotoBtn || !fileInput || !uploadBtn) return;
    
    // Click no botão da câmera abre o seletor de arquivo
    changePhotoBtn.addEventListener('click', function() {
        fileInput.click();
    });
    
    // Preview da imagem selecionada
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        
        // Validações
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use PNG, JPEG ou JPG');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            alert('Arquivo muito grande. Máximo 5MB');
            return;
        }
        
        // Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            profileImages.forEach(img => {
                img.src = e.target.result;
            });
        };
        reader.readAsDataURL(file);
        
        // Mostrar botão de upload
        uploadBtn.style.display = 'block';
    });
    
    // Upload da foto
    uploadBtn.addEventListener('click', function() {
        const file = fileInput.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('foto_perfil', file);
        
        // Mostrar loading
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';
        uploadBtn.disabled = true;
        
        fetch('../api/upload_profile_photo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sucesso
                uploadBtn.innerHTML = '<i class="fas fa-check mr-1"></i>Salvo!';
                uploadBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                uploadBtn.classList.add('bg-green-500');
                
                // Esconder botão após 2 segundos
                setTimeout(() => {
                    uploadBtn.style.display = 'none';
                    uploadBtn.innerHTML = '<i class="fas fa-upload mr-1"></i>Salvar';
                    uploadBtn.classList.remove('bg-green-500');
                    uploadBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    uploadBtn.disabled = false;
                }, 2000);
                
                // Recarregar página para atualizar todas as imagens
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Erro: ' + data.message);
                uploadBtn.innerHTML = '<i class="fas fa-upload mr-1"></i>Salvar';
                uploadBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao fazer upload da foto');
            uploadBtn.innerHTML = '<i class="fas fa-upload mr-1"></i>Salvar';
            uploadBtn.disabled = false;
        });
    });
});