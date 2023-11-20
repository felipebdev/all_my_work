import { StyleSheet, Text, View, ScrollView, TouchableOpacity, Image, } from 'react-native'
import React from 'react'
import Meusagendamentoscards from '../Cards/Meusagendamentoscards'

const Meusagendamentos = () => {

    let data = [
        {
            id: 1,
            username: 'Balvin',
            profile_image: "https://th.bing.com/th/id/OIP.Km7S0jP80f6Qn2LMrfKLRQHaFd?pid=ImgDet&rs=1",
          
        },
       
    ]
        
    

    // console.log(data[0].username)
    return (
        <ScrollView style={styles.container}>

            {
                data.map((item) => {
                    return (
                        <Meusagendamentoscards
                            key={item.id}
                            username={item.username}
                            profile_image={item.profile_image}
                            
                           
                        />
                    )
                })
            }
        </ScrollView>
    )
}

export default Meusagendamentos

const styles = StyleSheet.create({
    container: {
        width: '100%',
        flexDirection: 'column',
    }
})