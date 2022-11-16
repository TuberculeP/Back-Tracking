document.querySelector('input#search').addEventListener('keyup', function(){
    document.querySelector('ul').remove();
    document.querySelector('main').appendChild(document.createElement('ul'))
    if(this.value === ''){
        document.querySelector('p.result').innerHTML = "";
    }else{

        axios.get('https://api.themoviedb.org/3/search/movie?query=' + this.value + '&api_key=d3151e4e15cfce47f5840fd3c57988df')
            .then(response => {
                document.querySelector('p.result').innerHTML = "Résultats : " + response.data['total_results'];
                response.data['results'].forEach(result => {
                    document.querySelector('ul').appendChild(document.createElement('li'));
                    document.querySelector('li:last-child').innerHTML = result['original_title'];
                })
            })
            .catch(error => console.error(error));
    }


})