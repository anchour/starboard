export const getTagNameFromBlockName = (block) => {
    let tagName = '';

    switch (block.name) {
        case 'core/heading':
            tagName = `h${block.attributes.level}`;
            break;
        case 'core/list':
            tagName = block.attributes.ordered ? 'ol' : 'ul';
            break;
        case 'core/list-item':
            tagName = 'li';
            break;
        case 'core/paragraph':
            tagName = 'p';
            break;
    }

    return tagName;
}
