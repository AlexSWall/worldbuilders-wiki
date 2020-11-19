import React, { Component } from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import AdministrationPanel from './Components/AdministrationPanel';

class AdministrationApp extends Component
{
	render()
	{
		return (
			<div id="pageWrapper">
				<NavigationBar authenticationData={this.props.authenticationData}/>
				<Flash flash={this.props.flash}/>
				<main>
					<div id="contentWrapper">
						<div id="content">
							<div id="mainPanelWrapper">
								<div id="mainPanel">
									<AdministrationPanel authenticationData={this.props.authenticationData}/>
								</div> {/* mainPanelWrapper */}
							</div> {/* mainPanel */}
						</div> {/* content */}
					</div> {/* contentWrapper */}
				</main> {/* main */}
			</div> /* pageWrapper */
		);
	}
}

export default AdministrationApp;
