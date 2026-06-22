export const getViewport = () => {
    return {
        width: window.innerWidth,
        height: window.innerHeight
    };
};

export const getViewportWithoutScrollbar = () => {
    return {
        width: document.documentElement.clientWidth,
        height: document.documentElement.clientHeight
    };
};
