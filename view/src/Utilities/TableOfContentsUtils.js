function renderTableOfContents(pageName, htmlString)
{
	const [generatedContent, toc] = extractTableOfContents(`#${pageName}`, htmlString);
	return generatedContent.replace(
		'[[Table of Contents]]',
		`<div id="toc">${toc}</div>`
	);
}

function extractTableOfContents(url, content)
{
	if ( content == null )
		return ["", ""];

	let toc = "<h2>Table of Contents</h2>";
	let level = 1;
	let isEmpty = true;

	const generatedContent = content.replace(
		/<h([2-6])>([^<]+)<\/h([2-6])>/gi,
		function (str, openLevel, titleText, closeLevel) 
		{
			if (openLevel != closeLevel)
				return str;

			isEmpty = false;

			if (openLevel > level)
				toc += (new Array(openLevel - level + 1)).join("<ul>");
			else if (openLevel < level)
				toc += (new Array(level - openLevel + 1)).join("</ul>");

			level = parseInt(openLevel);

			const anchor = titleText.replace(/ /g, "_");
			toc += `<li><a href="${url}#${anchor}">${titleText}</a></li>`;

			return `<h${openLevel}><a class="anchor" id="${anchor}">${titleText}</a></h${closeLevel}>`;
		}
	);

	if (!isEmpty)
	{
		if (level) 
			toc += (new Array(level + 1)).join("</ul>");
	}
	else
		toc = "";

	return [generatedContent, toc];
}

export default renderTableOfContents;