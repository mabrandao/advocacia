-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/12/2024 às 02:39
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `agencia`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) UNSIGNED NOT NULL,
  `processo_id` int(11) UNSIGNED DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `data_hora` datetime NOT NULL,
  `local` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('agendada','realizada','cancelada','adiada') DEFAULT 'agendada',
  `observacoes` text DEFAULT NULL,
  `link_virtual` varchar(255) DEFAULT NULL,
  `lembrete_enviado` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `andamentos_processo`
--

CREATE TABLE `andamentos_processo` (
  `id` int(11) UNSIGNED NOT NULL,
  `processo_id` int(11) UNSIGNED NOT NULL,
  `data_andamento` datetime NOT NULL,
  `tipo_andamento` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `arquivo_anexo` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `areas_atuacao`
--

CREATE TABLE `areas_atuacao` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `tabela` varchar(100) NOT NULL,
  `acao` varchar(255) NOT NULL,
  `dados` text DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `cpf_cnpj` varchar(20) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(8) DEFAULT NULL,
  `profissao` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conversas`
--

CREATE TABLE `conversas` (
  `id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `assunto` varchar(255) DEFAULT NULL,
  `processo_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `data_ultima_mensagem` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `processo_id` int(11) UNSIGNED DEFAULT NULL,
  `cliente_id` int(11) UNSIGNED DEFAULT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `tamanho_arquivo` varchar(50) DEFAULT NULL,
  `formato` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `faturas`
--

CREATE TABLE `faturas` (
  `id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `processo_id` int(11) UNSIGNED NOT NULL,
  `numero_fatura` varchar(50) DEFAULT NULL,
  `parcela` int(11) DEFAULT NULL,
  `total_parcelas` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `juros` decimal(10,2) DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `status_pagamento` enum('pendente','pago','parcelado','cancelado') DEFAULT 'pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `vencimento` date DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `comprovante_pagamento` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_sistema`
--

CREATE TABLE `logs_sistema` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `acao` varchar(255) NOT NULL,
  `tabela` varchar(100) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `dados` text DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) UNSIGNED NOT NULL,
  `conversa_id` int(11) UNSIGNED NOT NULL,
  `remetente_id` int(11) UNSIGNED NOT NULL,
  `conteudo` text NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `processos`
--

CREATE TABLE `processos` (
  `id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `area_atuacao_id` int(11) UNSIGNED NOT NULL,
  `numero_processo` varchar(20) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `valor_causa` decimal(10,2) DEFAULT NULL,
  `honorarios` decimal(10,2) DEFAULT NULL,
  `statos` enum('pendente','em_analise','respondido','finalizado') DEFAULT 'pendente',
  `prioridade` enum('baixa','media','alta') DEFAULT 'baixa',
  `data_distribuicao` date DEFAULT NULL,
  `data_conclusao` date DEFAULT NULL,
  `comarca` varchar(100) DEFAULT NULL,
  `vara` varchar(100) DEFAULT NULL,
  `juiz` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `processo_documentos`
--

CREATE TABLE `processo_documentos` (
  `id` int(11) UNSIGNED NOT NULL,
  `processo_id` int(11) UNSIGNED NOT NULL,
  `documento_id` int(11) UNSIGNED NOT NULL,
  `tipo_documento` varchar(50) NOT NULL,
  `data_upload` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `versao` int(11) NOT NULL,
  `hash_arquivo` varchar(255) DEFAULT NULL,
  `tamanho_arquivo` varchar(50) DEFAULT NULL,
  `usuario_upload_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `processo_profissionais`
--

CREATE TABLE `processo_profissionais` (
  `id` int(11) UNSIGNED NOT NULL,
  `processo_id` int(11) UNSIGNED NOT NULL,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `papel` varchar(50) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `processo_tags`
--

CREATE TABLE `processo_tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `processo_id` int(11) UNSIGNED NOT NULL,
  `tag` varchar(50) NOT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `prioridade` int(11) DEFAULT NULL,
  `usuario_criacao_id` int(11) UNSIGNED NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `estado_civil` varchar(20) DEFAULT NULL,
  `profissao` varchar(100) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(8) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `telefone_comercial` varchar(20) DEFAULT NULL,
  `telefone_alternativo` varchar(20) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `token_assinatura` varchar(100) DEFAULT NULL,
  `token_expiracao` datetime DEFAULT NULL,
  `assinatura_digital` text DEFAULT NULL,
  `profile_picture` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes`
--

CREATE TABLE `solicitacoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `tipo` enum('orcamento','duvida','outros') DEFAULT 'outros',
  `area_atuacao_id` int(11) UNSIGNED NOT NULL,
  `mensagem` text NOT NULL,
  `arquivos_anexos` text DEFAULT NULL,
  `status` enum('pendente','em_analise','respondido','finalizado') DEFAULT 'pendente',
  `prioridade` enum('baixa','media','alta') DEFAULT 'media',
  `responsavel_id` int(11) UNSIGNED DEFAULT NULL,
  `data_resposta` date DEFAULT NULL,
  `resposta` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_permissao`
--

CREATE TABLE `tipos_permissao` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `tipo_acesso` enum('admin','advogado','cliente') DEFAULT 'cliente',
  `ver` tinyint(1) DEFAULT 0,
  `adicionar` tinyint(1) DEFAULT 0,
  `editar` tinyint(1) DEFAULT 0,
  `excluir` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','advogado','cliente') DEFAULT 'cliente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `ultimo_acesso` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processo_id` (`processo_id`);

--
-- Índices de tabela `andamentos_processo`
--
ALTER TABLE `andamentos_processo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processo_id` (`processo_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `areas_atuacao`
--
ALTER TABLE `areas_atuacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `conversas`
--
ALTER TABLE `conversas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `processo_id` (`processo_id`);

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processo_id` (`processo_id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `faturas`
--
ALTER TABLE `faturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_processo_id` (`processo_id`);

--
-- Índices de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversa_id` (`conversa_id`),
  ADD KEY `idx_remetente_id` (`remetente_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `processos`
--
ALTER TABLE `processos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_area_atuacao_id` (`area_atuacao_id`);

--
-- Índices de tabela `processo_documentos`
--
ALTER TABLE `processo_documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processo_id` (`processo_id`),
  ADD KEY `idx_documento_id` (`documento_id`),
  ADD KEY `idx_usuario_upload_id` (`usuario_upload_id`);

--
-- Índices de tabela `processo_profissionais`
--
ALTER TABLE `processo_profissionais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processo_id` (`processo_id`),
  ADD KEY `idx_profissional_id` (`profissional_id`);

--
-- Índices de tabela `processo_tags`
--
ALTER TABLE `processo_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_processo_id` (`processo_id`),
  ADD KEY `idx_usuario_criacao_id` (`usuario_criacao_id`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_area_atuacao_id` (`area_atuacao_id`);

--
-- Índices de tabela `tipos_permissao`
--
ALTER TABLE `tipos_permissao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `andamentos_processo`
--
ALTER TABLE `andamentos_processo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `areas_atuacao`
--
ALTER TABLE `areas_atuacao`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conversas`
--
ALTER TABLE `conversas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `faturas`
--
ALTER TABLE `faturas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `processos`
--
ALTER TABLE `processos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `processo_documentos`
--
ALTER TABLE `processo_documentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `processo_profissionais`
--
ALTER TABLE `processo_profissionais`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `processo_tags`
--
ALTER TABLE `processo_tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipos_permissao`
--
ALTER TABLE `tipos_permissao`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `andamentos_processo`
--
ALTER TABLE `andamentos_processo`
  ADD CONSTRAINT `andamentos_processo_ibfk_1` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `andamentos_processo_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `conversas`
--
ALTER TABLE `conversas`
  ADD CONSTRAINT `conversas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversas_ibfk_2` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_ibfk_2` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_ibfk_3` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `faturas`
--
ALTER TABLE `faturas`
  ADD CONSTRAINT `faturas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faturas_ibfk_2` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD CONSTRAINT `logs_sistema_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `processos`
--
ALTER TABLE `processos`
  ADD CONSTRAINT `processos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `processos_ibfk_2` FOREIGN KEY (`area_atuacao_id`) REFERENCES `areas_atuacao` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `processo_documentos`
--
ALTER TABLE `processo_documentos`
  ADD CONSTRAINT `processo_documentos_ibfk_1` FOREIGN KEY (`usuario_upload_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `processo_documentos_ibfk_2` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `processo_documentos_ibfk_3` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `processo_profissionais`
--
ALTER TABLE `processo_profissionais`
  ADD CONSTRAINT `processo_profissionais_ibfk_1` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `processo_profissionais_ibfk_2` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `processo_tags`
--
ALTER TABLE `processo_tags`
  ADD CONSTRAINT `processo_tags_ibfk_1` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `processo_tags_ibfk_2` FOREIGN KEY (`usuario_criacao_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `profissionais`
--
ALTER TABLE `profissionais`
  ADD CONSTRAINT `profissionais_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD CONSTRAINT `solicitacoes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitacoes_ibfk_2` FOREIGN KEY (`area_atuacao_id`) REFERENCES `areas_atuacao` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitacoes_ibfk_3` FOREIGN KEY (`area_atuacao_id`) REFERENCES `areas_atuacao` (`id`);

--
-- Restrições para tabelas `tipos_permissao`
--
ALTER TABLE `tipos_permissao`
  ADD CONSTRAINT `tipos_permissao_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
