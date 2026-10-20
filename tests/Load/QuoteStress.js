import http from "k6/http";
import { check, sleep } from "k6";

export const options = {
    vus: 10,
    duration: "20s",
};

const BASE_URL = "http://127.0.0.1:8000";
const TOKEN = "1|fxZKPVxiXxpbTaX20n934uLnRBbGccvxt45kenZP0bf38298";
const TENANT_CODE = "demo";

export default function () {
    const payload = JSON.stringify({
        insurance_product_id: 9,
        customer_id: 3,
        input_data: {
            vehicle_year: 1402,
            coverage_type: "third_party",
        },
    });

    const res = http.post(`${BASE_URL}/api/v1/quotes`, payload, {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            Authorization: `Bearer ${TOKEN}`,
            "X-Tenant-Code": TENANT_CODE,
        },
    });

    check(res, {
        "status is 201": (r) => r.status === 201,
    });

    sleep(1);
}
