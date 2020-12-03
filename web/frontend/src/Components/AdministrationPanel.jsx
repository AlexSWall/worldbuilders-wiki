import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

export default function AdministrationPanel({ children })
{
	const globals = useContext(GlobalsContext);

	return (
		<div className="card">
			<div className="card-header">
				<h1>Administration Panel — Home</h1>
			</div>
			<div className="card-body">
				<p style={ { marginBottom: '20px' } }>Administrators only.</p>
				<p><u><b>Authentication Data.</b></u></p>
				<dl style={ { listStyleType: 'none', marginLeft: '20px' } }>
					<dd><b>{ globals.isAuthenticated ? 'Is Authenticated. ' : 'Is Not Authenticated! ✗' }</b></dd>
					<dd><b>Preferred Name:</b></dd>
					<dt style={ { marginLeft: '20px' } }>{ globals.preferredName }</dt>
				</dl>
				{ children }
			</div>
		</div>
	);
}
