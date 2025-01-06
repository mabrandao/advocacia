-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/01/2025 às 05:22
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

--
-- Despejando dados para a tabela `agenda`
--

INSERT INTO `agenda` (`id`, `processo_id`, `tipo`, `data_hora`, `local`, `descricao`, `status`, `observacoes`, `link_virtual`, `lembrete_enviado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Audi├¬ncia', '2025-01-19 22:32:12', 'F├│rum Central', 'Primeira audi├¬ncia', 'agendada', NULL, NULL, 0, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 2, 'Reuni├úo', '2024-12-27 22:32:12', 'Escrit├│rio', 'Reuni├úo com cliente', 'agendada', NULL, NULL, 0, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 3, 'Prazo', '2025-01-04 22:32:12', 'N/A', 'Prazo para recurso', 'agendada', NULL, NULL, 0, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `andamentos_processo`
--

INSERT INTO `andamentos_processo` (`id`, `processo_id`, `data_andamento`, `tipo_andamento`, `descricao`, `usuario_id`, `arquivo_anexo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2024-12-20 22:32:12', 'Peti├º├úo Inicial', 'Protocolo da peti├º├úo inicial', 1, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 1, '2024-12-25 22:32:12', 'Despacho', 'Despacho inicial do juiz', 1, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 2, '2024-12-20 22:32:12', 'Audi├¬ncia', 'Designada audi├¬ncia inicial', 2, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `areas_atuacao`
--

INSERT INTO `areas_atuacao` (`id`, `nome`, `descricao`, `icone`, `ativo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Direito Civil', 'Processos de direito civil em geral', 'civil-icon', 1, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 'Direito Trabalhista', 'Causas trabalhistas e direitos do trabalho', 'trabalho-icon', 1, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 'Direito Familiar', 'Processos de fam├¡lia e sucess├Áes', 'familia-icon', 1, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `auditoria`
--

INSERT INTO `auditoria` (`id`, `usuario_id`, `tabela`, `acao`, `dados`, `data_hora`) VALUES
(1, 1, 'usuarios', 'criar', '{\"usuario_id\": 4}', '2024-12-20 22:32:12'),
(2, 2, 'processos', 'atualizar', '{\"processo_id\": 1}', '2024-12-20 22:32:12'),
(3, 3, 'documentos', 'deletar', '{\"documento_id\": 2}', '2024-12-20 22:32:12');

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

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `usuario_id`, `cpf_cnpj`, `rg`, `data_nascimento`, `telefone`, `celular`, `endereco`, `bairro`, `cidade`, `estado`, `cep`, `profissao`, `estado_civil`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, '123.456.789-00', '12.345.678-9', '1980-01-01', '(11) 3333-4444', '(11) 99999-8888', 'Rua A, 123', NULL, 'S├úo Paulo', 'SP', '01234567', NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 5, '987.654.321-00', '98.765.432-1', '1990-05-15', '(11) 4444-5555', '(11) 98888-7777', 'Rua B, 456', NULL, 'S├úo Paulo', 'SP', '04567890', NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 6, '456.789.123-00', '45.678.912-3', '1985-12-30', '(11) 5555-6666', '(11) 97777-6666', 'Rua C, 789', NULL, 'S├úo Paulo', 'SP', '06789012', NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `conversas`
--

INSERT INTO `conversas` (`id`, `cliente_id`, `assunto`, `processo_id`, `status`, `data_ultima_mensagem`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'D├║vidas sobre processo', 1, 'aberta', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 2, 'Documenta├º├úo pendente', 2, 'aberta', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 3, 'Agendamento de reuni├úo', 3, 'fechada', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `documentos`
--

INSERT INTO `documentos` (`id`, `nome`, `descricao`, `arquivo`, `tipo_documento`, `processo_id`, `cliente_id`, `usuario_id`, `categoria`, `tags`, `tamanho_arquivo`, `formato`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Peti├º├úo Inicial', 'Peti├º├úo inicial do processo', 'peticao_inicial_1.pdf', 'processo', 1, 1, 1, NULL, NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 'Procura├º├úo', 'Procura├º├úo do cliente', 'procuracao_1.pdf', 'documento', 1, 1, 1, NULL, NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 'Documentos Pessoais', 'Documentos pessoais do cliente', 'docs_pessoais_1.pdf', 'documento', 2, 2, 2, NULL, NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `faturas`
--

INSERT INTO `faturas` (`id`, `cliente_id`, `processo_id`, `numero_fatura`, `parcela`, `total_parcelas`, `valor`, `desconto`, `juros`, `valor_total`, `status_pagamento`, `metodo_pagamento`, `vencimento`, `data_pagamento`, `comprovante_pagamento`, `observacoes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'FAT-2024-001', NULL, NULL, 1000.00, NULL, NULL, 1000.00, 'pendente', NULL, '2025-01-19', NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 2, 2, 'FAT-2024-002', NULL, NULL, 2000.00, NULL, NULL, 2000.00, 'pago', NULL, '2025-01-04', NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 3, 3, 'FAT-2024-003', NULL, NULL, 1500.00, NULL, NULL, 1500.00, 'pendente', NULL, '2025-02-03', NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `logs_sistema`
--

INSERT INTO `logs_sistema` (`id`, `usuario_id`, `acao`, `tabela`, `registro_id`, `dados`, `ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'login', 'usuarios', 1, '{\"sucesso\": true}', '127.0.0.1', '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 2, 'criar', 'processos', 1, '{\"processo_id\": 1}', '127.0.0.1', '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 3, 'atualizar', 'documentos', 1, '{\"documento_id\": 1}', '127.0.0.1', '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `conversa_id`, `remetente_id`, `conteudo`, `lida`, `data_leitura`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Prezado cliente, como posso ajudar?', 1, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 1, 4, 'Gostaria de saber o andamento do processo', 0, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 2, 2, 'Favor enviar os documentos solicitados', 1, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `categoria` enum('Política','Esporte','Cidade','Oeste') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `image` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `galeria` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `noticias`
--

INSERT INTO `noticias` (`id`, `slug`, `categoria`, `titulo`, `image`, `content`, `galeria`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'novasmedidassoaprovadasnacmaramunicipaledt', 'Política', 'Novas medidas são aprovadas na câmara municipal-edt', 'politica1.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'galeria1.jpg', '2024-12-22 00:09:28', '2025-01-06 05:12:35', '2025-01-06 00:18:20'),
(2, 'esporte-regional-2024', 'Esporte', 'Time local se classifica para final do campeonato', 'esporte1.jpg', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'galeria2.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(3, 'cidade-obras-2024', 'Cidade', 'Prefeitura inicia obras de revitalização do centro', 'cidade1.jpg', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.', 'galeria3.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(4, 'oeste-desenvolvimento-2024', 'Oeste', 'Região Oeste recebe investimentos em infraestrutura', 'oeste1.jpg', 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.', 'galeria4.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', '0000-00-00 00:00:00'),
(5, 'politica-estadual-2024', 'Política', 'Assembleia aprova novo projeto de lei', 'politica2.jpg', 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.', 'galeria5.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', '0000-00-00 00:00:00'),
(6, 'esporte-copa-2024', 'Esporte', 'Atletas locais se destacam em competição nacional', 'esporte2.jpg', 'Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.', 'galeria6.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(7, 'cidade-cultura-2024', 'Cidade', 'Festival cultural movimenta a cidade', 'cidade2.jpg', 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.', 'galeria7.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(8, 'oeste-economia-2024', 'Oeste', 'Setor industrial da região oeste apresenta crescimento', 'oeste2.jpg', 'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.', 'galeria8.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(9, 'politica-nacional-2024', 'Política', 'Mudanças na legislação afetam município', 'politica3.jpg', 'Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.', 'galeria9.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(10, 'esporte-juventude-2024', 'Esporte', 'Projeto esportivo beneficia jovens da cidade', 'esporte3.jpg', 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'galeria10.jpg', '2024-12-22 00:09:28', '2024-12-22 00:09:28', NULL),
(11, '', 'Política', 'INTENÇÃO DE REGISTRO DE PREÇOS Nº 04/2023 – PROCESSO Nº 8.358/2023', '/assets/img/upload/Captura de tela 2024-04-09 203004.png', 'kijiknmln knoojn jnomlmlmm knolmlm oomolml', '/assets/img/upload/Captura de tela 2024-04-09 203004.png', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(16, 'inten----o-de-registro-de-pre--os-n---04-2023-----processo-n---8-358-2023', 'Política', 'INTENÇÃO DE REGISTRO DE PREÇOS Nº 04/2023 – PROCESSO Nº 8.358/2023', '/assets/img/upload/Captura de tela 2024-04-09 203004.png', 'lorem ipsum', '', '2025-01-06 04:15:00', '2025-01-06 04:15:00', NULL);

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

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `titulo`, `mensagem`, `tipo`, `lida`, `data_leitura`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Novo processo', 'Um novo processo foi cadastrado', 'sistema', 0, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 2, 'Prazo', 'Prazo processual pr├│ximo do vencimento', 'alerta', 0, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 3, 'Documento', 'Novo documento anexado ao processo', 'informacao', 1, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `processos`
--

INSERT INTO `processos` (`id`, `cliente_id`, `area_atuacao_id`, `numero_processo`, `titulo`, `descricao`, `valor_causa`, `honorarios`, `statos`, `prioridade`, `data_distribuicao`, `data_conclusao`, `comarca`, `vara`, `juiz`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '0001234-12.2024.8.26', 'A├º├úo de Indeniza├º├úo', 'Processo de indeniza├º├úo por danos morais', 50000.00, 5000.00, 'pendente', 'media', NULL, NULL, NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 2, 2, '0002345-23.2024.8.26', 'Reclama├º├úo Trabalhista', 'Processo trabalhista - horas extras', 30000.00, 3000.00, 'em_analise', 'alta', NULL, NULL, NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 3, 3, '0003456-34.2024.8.26', 'Div├│rcio Consensual', 'Processo de div├│rcio', 10000.00, 2000.00, 'pendente', 'baixa', NULL, NULL, NULL, NULL, NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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

--
-- Despejando dados para a tabela `processo_documentos`
--

INSERT INTO `processo_documentos` (`id`, `processo_id`, `documento_id`, `tipo_documento`, `data_upload`, `status`, `observacoes`, `versao`, `hash_arquivo`, `tamanho_arquivo`, `usuario_upload_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'peti├º├úo', '2024-12-20 22:32:12', 'ativo', NULL, 1, NULL, NULL, 1, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 1, 2, 'procura├º├úo', '2024-12-20 22:32:12', 'ativo', NULL, 1, NULL, NULL, 1, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 2, 3, 'documento', '2024-12-20 22:32:12', 'ativo', NULL, 1, NULL, NULL, 2, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `status`, `ultimo_acesso`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin Sistema', 'admin@advocacia.com', '55a5e9e78207b4df8699d60886fa070079463547b095d1a05bc719bb4e6cd251', 'admin', 'ativo', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(2, 'Jo├úo Advogado', 'joao@advocacia.com', '55a5e9e78207b4df8699d60886fa070079463547b095d1a05bc719bb4e6cd251', 'advogado', 'ativo', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(3, 'Maria Advogada', 'maria@advocacia.com', '55a5e9e78207b4df8699d60886fa070079463547b095d1a05bc719bb4e6cd251', 'advogado', 'ativo', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(4, 'Pedro Cliente', 'pedro@email.com', '55a5e9e78207b4df8699d60886fa070079463547b095d1a05bc719bb4e6cd251', 'cliente', 'ativo', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(5, 'Ana Cliente', 'ana@email.com', '55a5e9e78207b4df8699d60886fa070079463547b095d1a05bc719bb4e6cd251', 'cliente', 'ativo', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL),
(6, 'Carlos Cliente', 'carlos@email.com', '55a5e9e78207b4df8699d60886fa070079463547b095d1a05bc719bb4e6cd251', 'cliente', 'ativo', NULL, '2024-12-20 22:32:12', '2024-12-20 22:32:12', NULL);

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
-- Índices de tabela `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `andamentos_processo`
--
ALTER TABLE `andamentos_processo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `areas_atuacao`
--
ALTER TABLE `areas_atuacao`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `conversas`
--
ALTER TABLE `conversas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `faturas`
--
ALTER TABLE `faturas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `processos`
--
ALTER TABLE `processos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `processo_documentos`
--
ALTER TABLE `processo_documentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
