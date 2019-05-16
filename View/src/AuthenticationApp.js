import React, { Component } from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import Footer from './Components/Footer';
import Sidebar from './Components/Sidebar';

import AuthenticationPanel from './Components/AuthenticationPanel';
import AuthForm from './Components/Authentication/AuthForm';

class AuthenticationApp extends Component
{
	render()
	{
		return (
			<div id="pageWrapper">
				<NavigationBar auth={this.props.auth}/>
				<main>
					<div id="contentWrapper">
						<div id="content">
							<div id="mainPanelWrapper">
								<div id="mainPanel">
									<AuthenticationPanel>
										<AuthForm 
											formProperties={this.props.formProperties}
										/>
									</AuthenticationPanel>
								</div> {/* mainPanelWrapper */}
							</div> {/* mainPanel */}
						</div> {/* content */}
					</div> {/* contentWrapper */}
				</main> {/* main */}
			</div> /* pageWrapper */
		);
	}
}

export default AuthenticationApp;