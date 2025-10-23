document.addEventListener('DOMContentLoaded', () => {
    const pesquisa = document.getElementById('pesquisa');
    const botaoPesquisa = document.getElementById('botaoPesquisa');
    const botaoAnterior = document.getElementById('botaoAnterior');
    const botaoProximo = document.getElementById('botaoProximo');
    const pokeInfo = document.getElementById('pokeInfo');
    const botoesNavegacao = document.querySelector('botoesNavegacao');
    const pokeImagem = document.getElementById('pokeImagem');
    const nomePokemon = document.getElementById('nomePokemon');
    const mensagemErro = document.getElementById('mensagemErro');
    const mensagemLoading = document.getElementById('mensagemLoading');
    let idPokemonAtual = 1;

    const fetchPokemon = async (idPokemon) => {
        exibirLoading();
        try {
            const resposta = await fetch(`https://pokeapi.co/api/v2/pokemon/${idPokemon}`);

            if(!resposta.ok) throw new Error('Pokémon não encontrado! Tente novamente!');
            const pokemon = await resposta.json();
            preencherPokeInfo(pokemon);
            mensagemErro.classList.add('hidden');
            pokeInfo.classList.add('hidden');
        } catch(error) {
    
        }finally{

        }
    };

    const exibirLoading = () => {
        mensagemLoading.classList.remove('hidden');
        pokeInfo.classList.add('hidden');
        mensagemErro.classList.add('hidden');
        botoesNavegacao.classList.add('hidden');
    }

     const exibirErro = () => {
        nomePokemon.textContent = '';
        pokemonDescricao.textContent = '';
        pokeImagem.src = '';
        pokeInfo.classList.add('hidden');
        mensagemErro.classList.add('hidden');
        botoesNavegacao.classList.add('hidden');
    }

    }














);