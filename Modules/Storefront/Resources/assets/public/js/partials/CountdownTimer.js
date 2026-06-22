// partials/CountdownTimer.js

export class CountdownTimer {
    constructor(element) {
        this.element = element;

        if (this.element.querySelector('.countDays')) return;

        const dateEnd = this.element.dataset.dateEnd;
        if (!dateEnd) return;

        this.timestamp = this.parseDate(dateEnd);
        this.positions = [];

        this.initHTML();
        this.tick();
    }

    parseDate(dateString) {
        const cleanDate = dateString.trim();
        const safeDate = cleanDate.replace(/-/g, '/');
        let ts = new Date(safeDate).getTime();
        if (isNaN(ts)) {
            const parts = cleanDate.split('-');
            if (parts.length === 3) {
                ts = new Date(parts[0], parts[1] - 1, parts[2]).getTime();
            }
        }
        if (Date.now() > ts) {
            ts = Date.now() + (10 * 24 * 60 * 60 * 1000);
        }

        return ts;
    }

    initHTML() {
        this.element.classList.add('countdownHolder');

        const blocks = [
            { key: 'Days', label: 'Дней', digits: 3 },
            { key: 'Hours', label: 'Часов', digits: 2 },
            { key: 'Minutes', label: 'минут', digits: 2 },
            { key: 'Sec', label: 'сек', digits: 2 }
        ];

        let html = '';
        blocks.forEach(block => {
            html += `<span class="count${block.key}"><span class="num-time">`;
            for (let i = 0; i < block.digits; i++) {
                const extraClass = (block.key === 'Days' && i === 0) ? ' d-none' : '';
                html += `<span class="position${extraClass}"><span class="digit static">0</span></span>`;
            }
            html += `</span><span class="time_productany">${block.label}</span></span>`;
        });

        this.element.innerHTML = html;
        this.positions = Array.from(this.element.querySelectorAll('.position'));
    }

    tick() {
        if (isNaN(this.timestamp)) return;

        let left = Math.floor((this.timestamp - Date.now()) / 1000);
        if (left < 0) left = 0;

        const days = Math.floor(left / 86400);
        left -= days * 86400;

        const hours = Math.floor(left / 3600);
        left -= hours * 3600;

        const minutes = Math.floor(left / 60);
        left -= minutes * 60;

        const seconds = left;

        if (days > 99) this.positions[0].classList.remove('d-none');
        this.switchDigit(0, Math.floor(days / 100) % 10);
        this.switchDigit(1, Math.floor(days / 10) % 10);
        this.switchDigit(2, days % 10);

        this.switchDigit(3, Math.floor(hours / 10) % 10);
        this.switchDigit(4, hours % 10);

        this.switchDigit(5, Math.floor(minutes / 10) % 10);
        this.switchDigit(6, minutes % 10);

        this.switchDigit(7, Math.floor(seconds / 10) % 10);
        this.switchDigit(8, seconds % 10);

        if (this.timestamp - Date.now() > 0) {
            setTimeout(() => this.tick(), 1000);
        }
    }

    switchDigit(index, number) {
        const position = this.positions[index];
        if (!position || position.dataset.digit == number) return;

        position.dataset.digit = number;
        position.innerHTML = '';

        const newSpan = document.createElement('span');
        newSpan.className = 'digit';
        newSpan.textContent = number;

        position.appendChild(newSpan);
        setTimeout(() => {
            if (newSpan.parentNode) {
                newSpan.classList.add('static');
            }
        }, 100);
    }
}

export function initTimers() {
    document.querySelectorAll('.action-timer').forEach(el => {
        new CountdownTimer(el);
    });
}
