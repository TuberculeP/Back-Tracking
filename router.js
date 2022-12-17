// Vanilla SPA router by Mitch Wadair

/*
const route = (event) => {
    event = event || window.event;
    event.preventDefault();
    window.history.pushState({}, "", event.target.href);
    handleLocation();
};

const routes = {
};

const handleLocation = async () => {
    const path = window.location.pathname;
    const route = routes[path] || routes[404];
    document.querySelector('main').innerHTML = await fetch(route).then((data) => data.text());
};

window.onpopstate = handleLocation;
window.route = route;

handleLocation();