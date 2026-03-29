#!/usr/bin/env php
<?php
// Gera o hash bcrypt correto para "Admin@2025"
// O hash no schema.sql é um placeholder — use este para atualizar
$senha = 'Admin@2025';
$hash  = password_hash($senha, PASSWORD_BCRYPT);
echo "Senha: $senha\n";
echo "Hash:  $hash\n";
echo "\nSQL para atualizar no banco:\n";
echo "UPDATE usuarios SET senha_hash='$hash' WHERE email='admin@academia.com';\n";
