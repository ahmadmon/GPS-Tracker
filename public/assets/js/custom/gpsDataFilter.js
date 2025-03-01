class GPSFilter {
    constructor() {
        this.validPoints = []; // ذخیره نقاط معتبر
    }

    calculateHaversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const toRad = (angle) => angle * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c;
    }

    isValid(currentPoint) {
        const prevPoint = this.validPoints.length > 0 ? this.validPoints[this.validPoints.length - 1] : null;

        if (!prevPoint) {
            this.validPoints.push(currentPoint);
            return true;
        }

        const distance = this.calculateHaversineDistance(
            prevPoint.lat, prevPoint.lng,
            currentPoint.lat, currentPoint.lng
        );
        const timeDiff = (currentPoint.datetime - prevPoint.datetime) / 1000;
        const timeDiffHours = timeDiff / 3600;

        if (timeDiff > 1800) {
            this.validPoints.push(currentPoint);
            return true;
        }

        if (timeDiff <= 0 || timeDiff > 50) return false;

        if (distance <= 0.1 && Math.abs(currentPoint.speed) <= 10) return false;

        const maxPossibleDistance = currentPoint.speed >= 120
            ? 120 * timeDiffHours
            : Math.max(1, 0.5 * timeDiffHours * currentPoint.speed);
        if (distance > maxPossibleDistance) return false;

        const calculatedSpeed = distance / timeDiffHours;
        if (currentPoint.speed > 300 || calculatedSpeed > 300) return false;

        const validSpeed = Math.min(currentPoint.speed, calculatedSpeed);
        const prevSpeed = prevPoint.speed;
        const acceleration = Math.abs(validSpeed - prevSpeed) / timeDiffHours;
        if (acceleration > 20) return false;

        this.validPoints.push(currentPoint);
        return true;
    }

    filterGPSData(gpsData) {
        this.validPoints = [];

        gpsData.forEach(point => {
            if (this.isValid(point)) {
                this.validPoints.push(point);
            }
        });

        return this.validPoints;
    }

}
