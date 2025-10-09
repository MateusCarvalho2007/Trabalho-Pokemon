-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/10/2025 às 21:50
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
-- Estrutura para tabela `equipe`
--

CREATE TABLE `equipe` (
  `idTreinador` int(11) NOT NULL,
  `idPokemon` int(11) NOT NULL,
  `idEquipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pokemon`
--

CREATE TABLE `pokemon` (
  `idPokemon` int(11) NOT NULL,
  `idTreinador` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `tipo` text NOT NULL
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `troca`
--

CREATE TABLE `troca` (
  `idTreinador1` int(11) NOT NULL,
  `idTreinador2` int(11) NOT NULL,
  `idPokemon1` int(11) NOT NULL,
  `idTroca` int(11) NOT NULL,
  `idPokemon2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`idEquipe`),
  ADD KEY `idPokemon` (`idPokemon`),
  ADD KEY `idTreinador` (`idTreinador`);

--
-- Índices de tabela `pokemon`
--
ALTER TABLE `pokemon`
  ADD PRIMARY KEY (`idPokemon`),
  ADD KEY `pokemon_ibfk_1` (`idTreinador`);

--
-- Índices de tabela `treinador`
--
ALTER TABLE `treinador`
  ADD PRIMARY KEY (`idTreinador`);

--
-- Índices de tabela `troca`
--
ALTER TABLE `troca`
  ADD PRIMARY KEY (`idTroca`),
  ADD KEY `idTreinador1` (`idTreinador1`),
  ADD KEY `idTreinador2` (`idTreinador2`),
  ADD KEY `idPokemon` (`idPokemon1`),
  ADD KEY `idPokemon2` (`idPokemon2`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `equipe`
--
ALTER TABLE `equipe`
  MODIFY `idEquipe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pokemon`
--
ALTER TABLE `pokemon`
  MODIFY `idPokemon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `treinador`
--
ALTER TABLE `treinador`
  MODIFY `idTreinador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `troca`
--
ALTER TABLE `troca`
  MODIFY `idTroca` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `equipe_ibfk_1` FOREIGN KEY (`idPokemon`) REFERENCES `pokemon` (`idPokemon`),
  ADD CONSTRAINT `equipe_ibfk_2` FOREIGN KEY (`idTreinador`) REFERENCES `treinador` (`idTreinador`);

--
-- Restrições para tabelas `pokemon`
--
ALTER TABLE `pokemon`
  ADD CONSTRAINT `pokemon_ibfk_1` FOREIGN KEY (`idTreinador`) REFERENCES `treinador` (`idTreinador`);

--
-- Restrições para tabelas `troca`
--
ALTER TABLE `troca`
  ADD CONSTRAINT `troca_ibfk_1` FOREIGN KEY (`idTreinador1`) REFERENCES `treinador` (`idTreinador`),
  ADD CONSTRAINT `troca_ibfk_2` FOREIGN KEY (`idTreinador2`) REFERENCES `treinador` (`idTreinador`),
  ADD CONSTRAINT `troca_ibfk_3` FOREIGN KEY (`idPokemon1`) REFERENCES `pokemon` (`idPokemon`),
  ADD CONSTRAINT `troca_ibfk_4` FOREIGN KEY (`idPokemon2`) REFERENCES `pokemon` (`idPokemon`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
