// Função para verificar o estado de autenticação do usuário
function checkAuthentication() {
    // Simular estado de autenticação (substitua isso com lógica real)
    const isAuthenticated = false; // Substitua com lógica real para checar autenticação do usuário

    if (isAuthenticated) {
        document.getElementById('login-link').style.display = 'none';
        document.getElementById('register-link').style.display = 'none';
        document.getElementById('account-menu').style.display = 'block';
    } else {
        document.getElementById('login-link').style.display = 'block';
        document.getElementById('register-link').style.display = 'block';
        document.getElementById('account-menu').style.display = 'none';
    }
}

// Função para carregar produtos via AJAX
function loadProducts() {
    fetch('path/to/products.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar produtos: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const productGrid = document.querySelector('.novidades-grid'); // Certifique-se de que o seletor está correto
            if (data.products && data.products.length > 0) {
                data.products.forEach(product => {
                    const productCard = document.createElement('div');
                    productCard.className = 'novidade-item'; // Certifique-se de que a classe está correta
                    productCard.innerHTML = `
                        <img src="${product.image}" alt="${product.name}">
                        <div class="product-info">
                            <p class="product-name">${product.name}</p>
                            <p class="product-price">R$ ${product.price.toFixed(2)}</p>
                            <button class="buy-button">Comprar</button>
                            ${product.discount ? '<div class="discount-badge">Desconto</div>' : ''}
                        </div>
                    `;
                    productGrid.appendChild(productCard);
                });
            } else {
                productGrid.innerHTML = '<p>Nenhum produto encontrado.</p>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar produtos:', error);
            document.querySelector('.novidades-grid').innerHTML = '<p>Ocorreu um erro ao carregar os produtos.</p>';
        });
}

// Função para validação de formulário de login
function validateLoginForm(event) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (username === '' || password === '') {
        alert('Por favor, preencha todos os campos.');
        event.preventDefault(); // Impede o envio do formulário
    }
}

// Função para validação de formulário de registro
function validateRegisterForm(event) {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (username === '' || email === '' || password === '') {
        alert('Por favor, preencha todos os campos.');
        event.preventDefault(); // Impede o envio do formulário
    } else {
        alert('Registro realizado com sucesso!');
        // Aqui você pode adicionar mais lógica, como enviar os dados para o servidor
    }
}

// Inicializar funções quando o DOM estiver completamente carregado
document.addEventListener('DOMContentLoaded', function() {
    checkAuthentication(); // Verificar estado de autenticação
    loadProducts(); // Carregar produtos

    // Adicionar validação de formulário de login e registro
    document.getElementById('login-form')?.addEventListener('submit', validateLoginForm);
    document.getElementById('register-form')?.addEventListener('submit', validateRegisterForm);

    // Lógica para o botão "Sair"
    document.getElementById('logout')?.addEventListener('click', function() {
        // Lógica para fazer logout (por exemplo, removendo token de autenticação)
        console.log('Usuário desconectado');
        // Após o logout, você pode redirecionar ou recarregar a página
        window.location.reload();
    });
});
