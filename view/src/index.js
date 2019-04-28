import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';

import myApp from 'myApp'; /* Imports myApp variable from index.php. */

ReactDOM.render(<App auth={myApp.auth} wiki={myApp.wiki} flash={myApp.flash}/>, document.getElementById('root'));
