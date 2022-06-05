import React, { useContext } from 'react';

import { GlobalStateContext } from './GlobalState';

import NavigationBar from './Components/NavigationBar';

export default function AdministrationApp()
{
	const globalState = useContext( GlobalStateContext );

	return (
		<div id="pageWrapper">
			<NavigationBar />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div className="mainPanel">
								<div className="card">
									<div className="card-header">
										<h1>Administration Panel — Home</h1>
									</div>
									<div className="card-body">
										<p style={ { marginBottom: '20px' } }>Administrators only.</p>
										<p><u><b>Authentication Data.</b></u></p>
										<dl style={ { listStyleType: 'none', marginLeft: '20px' } }>
											<dd><b>{ globalState.isAuthenticated ? 'Is Authenticated. ' : 'Is Not Authenticated! ✗' }</b></dd>
											<dd><b>Preferred Name:</b></dd>
											<dt style={ { marginLeft: '20px' } }>{ globalState.preferredName }</dt>
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
