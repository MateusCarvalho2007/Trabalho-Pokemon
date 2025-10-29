-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/10/2025 às 21:05
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
-- Banco de dados: `trocaspokemon`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `pokemon`
--

CREATE TABLE `pokemon` (
  `idPokemon` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `tipo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pokemon`
--

INSERT INTO `pokemon` (`idPokemon`, `nome`, `descricao`, `tipo`) VALUES
(2, 'pikachu', 'rato eletrico', 'eletrico');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pokemontrocavel`
--

CREATE TABLE `pokemontrocavel` (
  `idPokemonTrocavel` int(11) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinador`
--

CREATE TABLE `treinador` (
  `idTreinador` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` text NOT NULL,
  `nome` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `treinador`
--

INSERT INTO `treinador` (`idTreinador`, `email`, `senha`, `nome`) VALUES
(1, 'ash@example.com', '$2y$10$C.zFe9F75aKUP2IUHPnAeua77vrOEqkvvs9mx4o7reIhLVVWDOE.W', 'Ash Ketchum');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `pokemon`
--
ALTER TABLE `pokemon`
  ADD PRIMARY KEY (`idPokemon`);

--
-- Índices de tabela `pokemontrocavel`
--
ALTER TABLE `pokemontrocavel`
  ADD PRIMARY KEY (`idPokemonTrocavel`);

--
-- Índices de tabela `treinador`
--
ALTER TABLE `treinador`
  ADD PRIMARY KEY (`idTreinador`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pokemon`
--
ALTER TABLE `pokemon`
  MODIFY `idPokemon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pokemontrocavel`
--
ALTER TABLE `pokemontrocavel`
  MODIFY `idPokemonTrocavel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `treinador`
--
ALTER TABLE `treinador`
  MODIFY `idTreinador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
