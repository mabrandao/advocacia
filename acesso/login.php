<?php
require_once 'includes/auth.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    if (login($email, $senha)) {
        header("Location: /index.php");
        exit;
    } else {
        $erro = "Email ou senha inválidos";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Login</h3>
            </div>
            <div class="card-body">
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Entrar</button>
                </form>
                
                <div class="mt-3">
                    <p>Ainda não tem conta? <a href="/cadastro.php">Cadastre-se</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
