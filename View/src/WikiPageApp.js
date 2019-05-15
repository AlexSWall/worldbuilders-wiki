import React, { Component } from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import Footer from './Components/Footer';
import Sidebar from './Components/Sidebar';
import WebpageLoader from './Components/WebpageLoader';

import WikiPanel from './Components/WikiPanel';
import AddWebpage from './Components/Special/Forms/AddWebpage'
import EditWebpage from './Components/Special/Forms/EditWebpage'
import DeleteWebpage from './Components/Special/Forms/DeleteWebpage'

class WikiPageApp extends Component
{
	render()
	{
		return (
			<div id="pageWrapper">
				<NavigationBar auth={this.props.auth}/>
				<Flash flash={this.props.flash}/>
				<main>
					<div id="contentWrapper">
						<div id="content">
							<div id="mainPanelWrapper">
								<div id="mainPanel">
									<WebpageLoader 
										urlBase='http://localhost:8080/w/'
										componentMapper={(webpageName) =>
											{
												const map = {
													'Special:Add_Wiki_Page': AddWebpage,
													'Special:Edit_Wiki_Page': EditWebpage,
													'Special:Delete_Wiki_Page': DeleteWebpage
												};
												if (webpageName in map)
													return map[webpageName];
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