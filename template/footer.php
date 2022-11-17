<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const input = document.querySelector('input#search');
    const list = document.querySelector('header ul');
    const divSearch = document.querySelector('div.search-modal');
    const p = document.querySelector('p.result');
    console.log(input, list);
    if(input !== undefined && list !== undefined){
        input.addEventListener('keyup', function(){
            list.innerHTML = '';
            if(input.value === ''){
                p.innerHTML = "";
                divSearch.classList.add('hidden');
            }else{
                divSearch.classList.remove('hidden');
                axios.get('https://api.themoviedb.org/3/search/movie?query=' + this.value + '&api_key=d3151e4e15cfce47f5840fd3c57988df')
                    .then(response => {
                        p.innerHTML = "Résultats : " + response.data['total_results'];
                        if(response.data['total_results'] === 10000) {
                            p.innerHTML += '+'
                        }
                        response.data['results'].forEach(result => {
                            if(document.querySelectorAll('li').length < 5){
                                list.appendChild(document.createElement('li'));
                                document.querySelector('li:last-child').innerHTML = '<a href="./movie.php?id='+result['id']+'">'+result['original_title']+'</a>';
                            }
                        })
                    })
                    .catch(error => console.error(error));
            }
        })
    }
</script>
</body>
</html>