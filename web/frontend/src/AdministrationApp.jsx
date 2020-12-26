import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';

export default function AdministrationApp()
{
	const globals = useContext(GlobalsContext);

	return (
		<div id="pageWrapper">
			<NavigationBar />
			<Flash />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div id="mainPanel">
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
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	);
}
