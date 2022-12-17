<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const input = document.querySelector('input#search');
    const list = document.querySelector('header ul.resultMovie');
    const list2 = document.querySelector('header ul.resultUser');
    const divSearch = document.querySelector('div.search-modal');
    const p = document.querySelector('p.resultMovie');
    const p2 = document.querySelector('p.resultUser');
    if(input !== undefined && list !== undefined){
        input.addEventListener('keyup', function(){
            
            list.innerHTML = '';
            if(input.value === ''){
                p.innerHTML = "";
                divSearch.classList.add('hidden');
            }else{
                divSearch.classList.remove('hidden');
                axios.get('https://api.themoviedb.org/3/search/movie?query='
                    + this.value + '&api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr')
                    .then(response => {
                        p.innerHTML = "Films : " + response.data['total_results'];
                        if(response.data['total_results'] === 10000) {
                            p.innerHTML += '+'
                        }
                        response.data['results'].forEach(result => {
                            if(document.querySelectorAll('ul.resultMovie li').length < 5){
                                list.appendChild(document.createElement('li'));
                                document.querySelector('ul.resultMovie li:last-child').innerHTML =
                                    '<a href="./movie.php?id='+result['id']+'">'+result['title']+'</a>';
                            }
                        })
                    })
                    .catch(error => console.error(error));
            }

            list2.innerHTML = '';
            if(input.value === ''){
                p2.innerHTML = "";
                divSearch.classList.add('hidden');
            }else{
                divSearch.classList.remove('hidden');
                axios.get('./api/user?query=' + this.value)
                    .then(response => {
                        p2.innerHTML = "Utilisateurs : " + response.data['total_result'];
                        if(response.data['total_result'] === 10000) {
                            p2.innerHTML += '+'
                        }
                        response.data['users'].forEach(result => {
                            if(document.querySelectorAll('ul.resultUser li').length < 5){
                                list2.appendChild(document.createElement('li'));
                                document.querySelector('ul.resultUser li:last-child').innerHTML =
                                    '<a href="./profile.php?id='+result['id']+'">'+result['pseudo']+'</a>';
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