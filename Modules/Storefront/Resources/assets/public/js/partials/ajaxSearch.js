export async function getAjaxLiveSearch(request, response) {

    let url = `${window.Korf.data.baseUrl}/suggestions?query=${encodeURIComponent(request)}`;

    try {
        const fetchResponse = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!fetchResponse.ok) {
            throw new Error(`Ошибка сервера: ${fetchResponse.status}`);
        }
        const json = await fetchResponse.json();
        response(json);
        setTimeout(() => {
            if (typeof window.addTimer === 'function') {
                window.addTimer();
            } else if (typeof addTimer === 'function') {
                addTimer();
            }
        }, 100);

    } catch (error) {
        console.error('Ошибка живого поиска:', error);
        if (typeof response === 'function') {
            response([]);
        }
    }
}
