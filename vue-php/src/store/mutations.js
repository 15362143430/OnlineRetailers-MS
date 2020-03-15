import * as types from "./mutations-type";

const mutations = {
    [types.setRoleID](state, role_id) {
        state.role_id = role_id;
    }
}

export default mutations