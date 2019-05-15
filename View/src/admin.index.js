import React from 'react';
import ReactDOM from 'react-dom';
import AdminApp from './AdminApp';

import myApp from 'myApp'; /* Imports myApp variable from index.php. */

ReactDOM.render(<AdminApp auth={myApp.auth} flash={myApp.flash}/>, document.getElementById('root'));
