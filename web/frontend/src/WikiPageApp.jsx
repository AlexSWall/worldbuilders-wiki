import React, { Component } from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import Footer from './Components/Footer';
import Sidebar from './Components/Sidebar';
import WebpageLoader from './Components/WebpageLoader';

import WikiPanel from './Components/WikiPanel';
import AddWebpage from './Components/Special/Forms/AddWebpage'

class WikiPageApp extends Component
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
									<WebpageLoader 
										urlBase='/w/'
										componentMapper={(path) =>
											{
												if (path === 'Special:Add_Wiki_Page')
													return AddWebpage;
												else
													return WikiPanel;
											}}
									/>
								</div> {/* mainPanelWrapper */}
							</div> {/* mainPanel */}
						</div> {/* content */}
					</div> {/* contentWrapper */}
				</main> {/* main */}
				<Footer />
				<Sidebar />
			</div> /* pageWrapper */
		);
	}
}

export default WikiPageApp;
