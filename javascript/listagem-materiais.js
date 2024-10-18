// Função para buscar os materiais da turma do aluno
function fetchMateriais() {
    fetch('http://localhost/teste/backend/get_materiais.php', {
        method: 'GET',
        credentials: 'include' // Envia o cookie na requisição
    })
    .then(response => response.json())
    .then(materiais => {
        const materiaList = document.querySelector('.materia-list');
        materiaList.innerHTML = ''; // Limpa a lista de matérias

        if (materiais.length > 0) {
            // Organiza os materiais por disciplina
            const disciplinas = {};
            materiais.forEach(material => {
                if (!disciplinas[material.nome_disciplina]) {
                    disciplinas[material.nome_disciplina] = [];
                }
                disciplinas[material.nome_disciplina].push(material);
            });

            // Para cada disciplina, cria os elementos HTML
            Object.keys(disciplinas).forEach(disciplina => {
                const li = document.createElement('li');
                li.id = disciplina.toLowerCase(); // Define o ID como o nome da disciplina em minúsculas
                li.classList.add('item-materia-list');

                // Cria o input e label
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = `input-${disciplina.toLowerCase()}`;
                input.id = `input-${disciplina.toLowerCase()}`;

                const label = document.createElement('label');
                label.setAttribute('for', `input-${disciplina.toLowerCase()}`);
                label.textContent = disciplina;

                // Cria o div para os conteúdos
                const conteudosDiv = document.createElement('div');
                conteudosDiv.id = 'conteudos';

                // Adiciona os materiais para essa disciplina
                disciplinas[disciplina].forEach(material => {
                    const materialContainer = document.createElement('div');
                    materialContainer.classList.add('material-container');

                    const tituloMaterial = document.createElement('h3');
                    tituloMaterial.classList.add('titulo-material');
                    tituloMaterial.textContent = material.nome_material;

                    const criadorMaterial = document.createElement('span');
                    criadorMaterial.classList.add('criador-material');
                    criadorMaterial.textContent = `Criado por: ${material.nome_usuario}`;

                    const dataMaterial = document.createElement('span');
                    dataMaterial.classList.add('data-material');
                    dataMaterial.textContent = `Data: ${material.data_material}`;

                    const descMaterial = document.createElement('p');
                    descMaterial.classList.add('desc-material');
                    descMaterial.textContent = material.descricao_material;

                    // Adiciona os elementos dentro do container de material
                    materialContainer.appendChild(tituloMaterial);
                    materialContainer.appendChild(criadorMaterial);
                    materialContainer.appendChild(dataMaterial);
                    materialContainer.appendChild(descMaterial);

                    // Adiciona os arquivos, se houver
                    if (material.arquivos && material.arquivos.length > 0) {
                        const arquivosDiv = document.createElement('div');
                        arquivosDiv.classList.add('arquivos');

                        const arquivosTitulo = document.createElement('h4');
                        arquivosTitulo.textContent = 'Arquivos disponíveis:';
                        arquivosDiv.appendChild(arquivosTitulo);

                        material.arquivos.forEach(arquivo => {
                            const arquivoNome = document.createElement('span');
                            arquivoNome.textContent = arquivo.nome_arquivo;
                            arquivoNome.style.cursor = 'pointer'; // Muda o cursor para indicar que é clicável
                            arquivoNome.classList.add('link-arquivo');
                            arquivoNome.dataset.arquivo = arquivo.nome_arquivo; // Armazena o nome do arquivo como um atributo de dados

                            // Adiciona evento de clique
                            arquivoNome.addEventListener('click', () => {
                                fetchArquivo(arquivo.nome_arquivo);
                            });

                            arquivosDiv.appendChild(arquivoNome);
                            arquivosDiv.appendChild(document.createElement('br')); // Para quebrar linha
                        });

                        materialContainer.appendChild(arquivosDiv);
                    } else {
                        // Se não houver arquivos, exibe a mensagem
                        const semArquivos = document.createElement('p');
                        semArquivos.textContent = 'Nenhum arquivo disponível para este material.';
                        materialContainer.appendChild(semArquivos);
                    }

                    // Adiciona o container de material ao div de conteúdos
                    conteudosDiv.appendChild(materialContainer);
                });

                // Adiciona o input, label e conteúdos ao li
                li.appendChild(input);
                li.appendChild(label);
                li.appendChild(conteudosDiv);
                materiaList.appendChild(li);
            });
        } else {
            // Caso não tenha materiais, exibe uma mensagem
            const li = document.createElement('li');
            li.textContent = 'Sem materiais';
            materiaList.appendChild(li);
        }
    })
    .catch(error => console.error('Erro ao carregar materiais:', error));
}

// Função para baixar o arquivo
function fetchArquivo(nomeArquivo) {
    fetch(`http://localhost/teste/backend/baixar_arquivo.php?file=${encodeURIComponent(nomeArquivo)}`, {
        method: 'GET',
        credentials: 'include' // Envia o cookie na requisição
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao baixar o arquivo.');
        }
        return response.blob(); // Converte a resposta em um Blob
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob); // Cria um URL para o Blob
        const a = document.createElement('a');
        a.href = url;
        a.download = nomeArquivo; // Nome do arquivo para download
        document.body.appendChild(a); // Adiciona o link ao DOM
        a.click(); // Aciona o download
        a.remove(); // Remove o elemento após o download
        window.URL.revokeObjectURL(url); // Libera o URL
    })
    .catch(error => console.error('Erro ao baixar o arquivo:', error));
}


// Chama a função para buscar os materiais ao carregar a página
window.onload = fetchMateriais;
