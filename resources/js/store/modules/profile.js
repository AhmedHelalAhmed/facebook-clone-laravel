const state = {
    user: null,
    userStatus: null,
};

const getters = {
    user: state => {
        return state.user;
    } ,
    userStatus: state => {
        return state.userStatus;
    }
};

const actions = {
    fetchUser({commit, state}, userId) {
        commit('setUserStatus', 'loading');
        axios.get('/api/users/' + userId)
            .then(response => {
                commit('setUser', response.data);
                commit('setUserStatus', 'success');
            })
            .catch(error => {
                commit('setUserStatus', 'error');
            });
    }
};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    }
};

export default {
    state, getters, actions, mutations
}
