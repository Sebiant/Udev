<?php
// Simulando una entrada de contraseña
$password = "123";

// Hashear la contraseña
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Mostrar el hash
echo "Password original: $password<br>";
echo "Password hasheada: $hashedPassword<br>";

// Verificar
if (password_verify('123', $hashedPassword)) {
    echo "¡Todo bien, contraseña válida!";
} else {
    echo "Contraseña incorrecta, papá.";
}
?>
