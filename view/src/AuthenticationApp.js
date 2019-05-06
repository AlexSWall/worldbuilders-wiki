import React, { Component } from 'react';

import NavigationBar from './components/NavigationBar';
import Flash from './components/Flash';
import Footer from './components/Footer';
import Sidebar from './components/Sidebar';

import AuthenticationPanel from './components/AuthenticationPanel';
import AuthForm from './components/Authentication/AuthForm';

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
											csrfField={this.props.csrfField}
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