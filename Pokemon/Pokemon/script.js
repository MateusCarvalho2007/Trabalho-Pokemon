document.addEventListener('DOMContentLoaded', () => {
    const pesquisa = document.getElementById('pesquisa');
    const botaoPesquisa = document.getElementById('botaoPesquisa');
    const botaoAnterior = document.getElementById('botaoAnterior');
    const botaoProximo = document.getElementById('botaoProximo');
    const pokeInfo = document.getElementById('pokeInfo');
    const botoesNavegacao = document.querySelector('.botoesNavegacao');
    const pokeImagem = document.getElementById('pokeImagem');
    const nomePokemon = document.getElementById('nomePokemon');
    const descricaoPokemon = document.getElementById('descricaoPokemon');
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
            pokeInfo.classList.remove('hidden');
        } catch(error) {
            exibirErro();
        } finally {
            ocultarLoading();
        }
    };

    const preencherPokeInfo = (pokemon) => {
        pokeImagem.src = pokemon['sprites']['versions']['generation-v']['black-white']['animated']['front_default'];
        nomePokemon.textContent = `${pokemon.name} (#${pokemon.id})`;
        descricaoPokemon.textContent = `Altura: ${pokemon.height / 10}m 
                                        | Peso: ${pokemon.weight / 10}kg 
                                        | Tipo: ${pokemon.types.map(typeInfo => typeInfo.type.name).join(', ')}`;
        idPokemonAtual = pokemon.id;
        atualizarNavegacaoBtn();
        botoesNavegacao.classList.remove('hidden');
    };

    const exibirLoading = () => {
        mensagemLoading.classList.remove('hidden');
        pokeInfo.classList.add('hidden');
        mensagemErro.classList.add('hidden');
        botoesNavegacao.classList.add('hidden');
    }

    const ocultarLoading = () => {
        mensagemLoading.classList.add('hidden');
    };

    const exibirErro = () => {
        nomePokemon.textContent = '';
        descricaoPokemon.textContent = '';
        pokeImagem.src = '';
        pokeInfo.classList.add('hidden');
        mensagemErro.classList.remove('hidden');
        botoesNavegacao.classList.add('hidden');
    }

    const atualizarNavegacaoBtn = () => {
        botaoAnterior.disabled = (idPokemonAtual <= 1);
    };

    const atualizarBtnPesquisa = () => {
        botaoPesquisa.disabled = !pesquisa.value.trim();
    };

    botaoPesquisa.addEventListener('click', () => {
        const query = pesquisa.value.trim().toLowerCase();
        if(query) {
            fetchPokemon(query);
        }
    });

    pesquisa.addEventListener('input', atualizarBtnPesquisa);

    pesquisa.addEventListener('keypress', (event) => {
        if(event.key == 'Enter'){
            botaoPesquisa.click();
        }
    });

    botaoAnterior.addEventListener('click', () => {
        if(idPokemonAtual > 1){
            fetchPokemon(idPokemonAtual - 1);
        }
    });

    botaoProximo.addEventListener('click', () => {
        fetchPokemon(idPokemonAtual + 1);
    });

    fetchPokemon(idPokemonAtual);
    atualizarBtnPesquisa();

    });