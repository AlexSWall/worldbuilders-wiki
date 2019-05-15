import React from 'react';
import ReactDOM from 'react-dom';
import WikiPageApp from './WikiPageApp';

import myApp from 'myApp'; /* Imports myApp variable from index.php. */

ReactDOM.render(<WikiPageApp auth={myApp.auth} flash={myApp.flash}/>, document.getElementById('root'));
