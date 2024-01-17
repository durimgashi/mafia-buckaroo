const parseHTML = (htmlString) => {
    const parser = new DOMParser();
    const parsedHtml = parser.parseFromString(htmlString, 'text/html');

    let text = parsedHtml.body.textContent

    if (text !== undefined)
        return text

    console.log("Muti", text)

    return ''
}