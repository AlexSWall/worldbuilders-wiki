function renderTableOfContents(htmlString)
{
	var [generatedContent, toc] = extractTableOfContents(htmlString);
	return generatedContent.replace(
		'[[Table of Contents]]',
		`<div id="toc">${toc}</div>`
	);
}

function extractTableOfContents(content)
{
	if ( content == null )
		return ["", ""];

	var toc = "<h2>Table of Contents</h2>";
	var level = 1;
	var isEmpty = true;

	var generatedContent = content.replace(
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

			var anchor = titleText.replace(/ /g, "_");
			toc += "<li><a href=\"#" + anchor + "\">" + titleText + "</a></li>";

			return "<h" + openLevel + "><a class=\"anchor\" id=\"" + anchor + "\">"
				+ titleText + "</a></h" + closeLevel + ">";
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