document.addEventListener('DOMContentLoaded', () => {

    // 1. Interação do Menu Lateral
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            menuItems.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // 2. Comportamento das setas do Accordion (Expandir/Retrair)
    const toggleButtons = document.querySelectorAll('.toggle-accordion');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('svg');
            // Rotaciona a seta para cima/baixo para simular abertura
            if (icon.style.transform === 'rotate(180deg)') {
                icon.style.transform = 'rotate(0deg)';
                icon.style.transition = 'transform 0.3s ease';
            } else {
                icon.style.transform = 'rotate(180deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
    });

    // 3. Ações da Tabela de Requisitos (Visão Administrador)
    const btnExcluir = document.querySelectorAll('.btn-delete');
    btnExcluir.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const linha = this.closest('tr');
            const reqName = linha.querySelector('.req-name').innerText;
            
            if (confirm(`Atenção, Administrador:\n\nTem certeza que deseja excluir o requisito "${reqName}" do projeto E-commerce Alpha?`)) {
                linha.style.opacity = '0.5';
                setTimeout(() => linha.remove(), 200); // Remove com um leve delay visual
            }
        });
    });

    const btnEditar = document.querySelectorAll('.btn-edit');
    btnEditar.forEach(btn => {
        btn.addEventListener('click', function() {
            const linha = this.closest('tr');
            const reqName = linha.querySelector('.req-name').innerText;
            alert(`Abrindo formulário de edição para: ${reqName}`);
        });
    });

});