window.APP        = window.APP || {};
window.APP.apiUrl = 'http://127.0.0.1:9090';
window.APP.resourceList = {
    'make-move'   : '/api/board/make-move',
    'game-winner' : '/api/board/game-winner',
    'symbols'     : '/api/board/symbols'
};
window.APP.templateDir = '/src/component';