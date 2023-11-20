import { StyleSheet, Text, View, TouchableOpacity, ScrollView } from 'react-native'
import React from 'react'
import AgendamentoComponent from './AgendamentoComponent'
import { MaterialIcons } from '@expo/vector-icons';
import Meusagendamentos from './Meusagendamentos';

const Solicitacaoagendamento = ({ navigation }) => {
  return (
    <View style={{}}>
         <ScrollView>
         <View style={{flexDirection: 'row', marginTop: 20}}>

            <TouchableOpacity onPress={() => navigation.navigate('Perfil')} style={{marginTop: 0,
            marginStart: 15}}>
                    <MaterialIcons name="arrow-back-ios" size={27} color="black" />
                </TouchableOpacity>

                <Text style={{marginStart: 115, marginTop: 0,
            fontSize: 20, color: 'black', fontWeight: 'bold'}}>Agenda</Text>
                </View>

                  
                  <Text style={{marginTop: 25, marginStart: 30, fontWeight: 'bold',
                fontSize: 15}}>Solicitação de agendamento</Text>
                <View style={{height: 2, backgroundColor: '#ec230d',marginTop: 0, width: 210,
              marginStart: 23.5}}></View>

                <View style={{marginTop: 5}}>
      <AgendamentoComponent />
      </View>

      <Text style={{marginTop: 25, marginStart: 30, fontWeight: 'bold',
                fontSize: 15}}>Meus agendamentos</Text>
                <View style={{height: 2, backgroundColor: '#ec230d',marginTop: 0, width: 160,
              marginStart: 23.5}}></View>

<View style={{marginTop: 5}}>
      <Meusagendamentos />
      </View>

      </ScrollView>
    </View>
  )
}

export default Solicitacaoagendamento

const styles = StyleSheet.create({})